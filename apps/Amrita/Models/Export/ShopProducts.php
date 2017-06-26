<?php
namespace Amrita\Models\Export;

class ShopProducts extends \Netsuite\Models\BaseImportExport
{
    protected $__collection_name = 'netsuite.importproducts.restlet';
    protected $__config = array(
        'default_sort' => array(
            'ns_id' => 1 
        ) 
    );
    protected $__logCategory = 'ExportShopProducts';
    protected $__pageSize = 500;
    protected $__max_loops = 1;

    /**
     * Exporter should only run if it has items to export
     * AND the Netsuite Products Importer is done for today
     *
     * @return boolean
     */
    protected function canRun()
    {
        if (!$this->isImportComplete() 
            || !$this->hasItemsToExport()
        )
        {
            echo "Import not complete or no items to export, so halting export of shop products.";
            return false;
        }
        
        $currentStatus = $this->getCurrentStatus();
        
        // If the previous process is finished and it was last finished yesterday, run it again from the very beginning (reset current status)
        if (! empty( $currentStatus->is_finished ) && (date( 'Y-m-d', $currentStatus->{'finished.time'} ) <= date( 'Y-m-d', strtotime( 'yesterday' ) )))
        {
            $this->log( 'Restarting export.', 'INFO', $this->__logCategory );
            $this->resetCurrentStatus();
        
            return true;
        }
        
        // else if the process is == processing (another process is running this), then let the other process finish before running this
        elseif (!empty($currentStatus->is_processing)) 
        {
        	return false;
        }
        
        // else If the process is finished and it was finished today, don't clutter the logs.  don't do anything until tomorrow
        elseif (! empty( $currentStatus->is_finished ) && (date( 'Y-m-d', $currentStatus->{'finished.time'} ) == date( 'Y-m-d', strtotime( 'now' ) )))
        {
            return false;
        }
                
        // else If the import is finished, mark it as finished
        elseif (!empty($currentStatus->total_items) && (int) ($currentStatus->current_page) == (int) $currentStatus->total_pages)
        {
            $currentStatus->set( 'is_finished', true );
            $currentStatus->set( 'finished', \Dsc\Mongo\Metastamp::getDate( 'now' ) );
            $this->setCurrentStatus( $currentStatus->cast() );
            $this->log( 'Export is finished for today.', 'INFO', $this->__logCategory );
        
            return false;
        }        
        
        return true;
    }
    
    /**
     * TODO Get a count of the items that need to be exported
     * which means any products that have been updated in netsuite since the date this exporter last ran
     * 
     * @return boolean
     */
    protected function hasItemsToExport()
    {
        //$model = new \Netsuite\Models\Import\Products;
        //->setState('filter.do_not_import', false);
        //$total = $model->collection()->count( $model->conditions() );
        // TODO if $total, return true, else false
        
        return true;
    }

    /**
     * Checks if the \Netsuite\Models\Import\Products is complete
     * @return boolean
     */
    public function isImportComplete()
    {
        $importer = new \Amrita\Models\Import\Products;
        $model = new \Dsc\Mongo\Collections\Settings;
        $model->load(array('type' => $importer->type(), 'name' => 'amrita-models-import-products' ));
        $currentStatus = new \Netsuite\Models\Prefabs\CurrentStatus( $model->get('current_status') );
        if (! empty( $currentStatus->is_finished ) && (date( 'Y-m-d', $currentStatus->{'finished.time'} ) == date( 'Y-m-d', strtotime( 'now' ) )))
        {
            return true;
        }        
        
        return false;
    }
    
    /**
     * Are we in the middle of an incomplete process?
     * If so, let's just pick up where we left off.
     * This is used by ->run() to determine whether we ->startRunning() or ->continueRunning() 
     *  
     * @return boolean
     */
    protected function isRunning()
    {
        $currentStatus = $this->getCurrentStatus();
    
        if (! empty( $currentStatus->total_items ) && empty( $currentStatus->is_finished ))
        {
            return true;
        }
    
        return false;
    }
    
    /**
     * Start a new process
     * 
     * @return boolean
     */
    protected function startRunning()
    {
        // Here, mark is_processing = true in the $currentStatus object and save it
        $currentStatus = $this->getCurrentStatusObject();
        $currentStatus->set( 'is_processing', true );
        $this->setCurrentStatus( $currentStatus->cast() );
                
        $searchResponse = $this->fetchRecords();
        
        if (empty( $searchResponse->items ))
        {
            
            $errorMessage = "No Search Results";
            $this->log($errorMessage, 'INFO', $this->__logCategory);
            
            $return = false;
            $currentStatus = $this->getCurrentStatusObject();
            $currentStatus->set( 'is_processing', false );
            $currentStatus->set( 'is_finished', true );
            $currentStatus->set( 'finished', \Dsc\Mongo\Metastamp::getDate( 'now' ) );
            $this->setCurrentStatus( $currentStatus->cast() );
            
            \Dsc\System::instance()->addMessage( 'No search results' );
        }
        else
        {
            $return = true;
            
            $currentStatus = $this->getCurrentStatusObject();
            
            $errorMessage = "startRunning found " . $searchResponse->total_items . " records, but is only processing " . $searchResponse->items_per_page . " starting with " . (int) ( $currentStatus->current_page) * $searchResponse->items_per_page;
            $this->log( $errorMessage, 'INFO', $this->__logCategory );
                        
            $clone = clone $searchResponse;
            unset( $clone->items );            
            $currentStatus->bind($clone);

            try
            {
                $this->saveRecords( $searchResponse->items );
                $currentStatus->set( 'is_processing', false );
                $this->setCurrentStatus( $currentStatus->cast() );
            }
            catch ( \Exception $e )
            {
                $errorMessage = $e->getMessage();
                $this->log( $errorMessage, 'ERROR', $this->__logCategory );
            }
        }
        
        return $return;
    }

    /**
     * Continue an existing process
     * 
     * @return boolean
     */
    protected function continueRunning()
    {
        $currentStatus = $this->getCurrentStatus();
        
        if (! isset( $currentStatus->current_page ))
        {
            return $this->startRunning();
        }
        
        // Here, mark is_processing = true in the $currentStatus object and save it
        $currentStatus->set( 'is_processing', true );
        $this->setCurrentStatus( $currentStatus->cast() );
        
        $searchResponse = $this->fetchRecords( $currentStatus->current_page, null );
        
        if (empty( $searchResponse->items ))
        {
            $errorMessage = "No Search Results";
            $this->log($errorMessage, 'INFO', $this->__logCategory);
            
            $return = false;
            $currentStatus = $this->getCurrentStatusObject();
            $currentStatus->set( 'is_processing', false );
            $currentStatus->set( 'is_finished', true );
            $currentStatus->set( 'finished', \Dsc\Mongo\Metastamp::getDate( 'now' ) );
            $this->setCurrentStatus( $currentStatus->cast() );
            
            \Dsc\System::instance()->addMessage( 'No search results' );
        }
        else
        {
            $return = true;
            
            $errorMessage = "continueRunning found " . $searchResponse->total_items . " records, but is only processing " . $searchResponse->items_per_page . " starting with " . (int) ( $currentStatus->current_page) * $searchResponse->items_per_page;
            $this->log( $errorMessage, 'INFO', $this->__logCategory );
            
            $clone = clone $searchResponse;
            unset( $clone->items );
            $currentStatus = $this->getCurrentStatusObject();
            $currentStatus->bind($clone);
            
            try
            {
                $this->saveRecords( $searchResponse->items );
                $currentStatus->set( 'is_processing', false );
                $this->setCurrentStatus( $currentStatus->cast() );                
            }
            catch ( \Exception $e )
            {
                $errorMessage = $e->getMessage();
                $this->log( $errorMessage, 'ERROR', $this->__logCategory );
            }
        }
        
        return $return;
    }

    /**
     * 
     * @param string $searchId
     * @param string $pageIndex
     * @return unknown
     */
    protected function fetchRecords( $offset = null, $limit = null )
    {
        $currentStatus = $this->getCurrentStatus();
        
        $model = new \Netsuite\Models\Import\Products;
        
        if (empty($limit)) {
            $limit = $this->__pageSize;
        }
        $model->setState( 'list.limit', $limit );
        $model->setState( 'filter.imported_today', true );
        
        //$model->setState( 'filter.do_not_import', false );
        //$model->setState('list.offset', 4);
        //$model->setState('filter.has_shop_product', false);
        //$model->setState('filter.has_parent', true);
        //$model->setState('filter.netsuite_id', '14326'); // 13074 = borked colors
        //$model->setState('filter.parent', '14326'); // ->setState('filter.parent', '14288') // 8675 has a size // 5797 has earring_closure // 5797 has Plating

        if (! empty( $offset ))
        {
            $model->setState( 'list.offset', (int) $offset );
        }
        
        $searchResponse = $model->paginate();
        
        if (! empty( $searchResponse ))
        {
            if (!empty($offset)) 
            {
                $searchResponse->setCurrent($offset+1);
            }
            
            // $searchResponse = $paginated_data['subset'];
            // $message = $paginated_data['total'] . " records match your search.";
            // $message = \Dsc\Debug::dump($searchResponse);
            // \Dsc\System::instance()->addMessage( $message );
        }
        
        return $searchResponse;
    }

    protected function saveRecords( array $records )
    {
        $count = 0;
        $start = microtime( true );
        foreach ( $records as $record )
        {
            set_time_limit( 0 );
            $this->clearErrors();
            
            try
            {
                if ($result = $this->exportItem( $record ))
                {
                    $count ++;
                }
                else
                {
                    $messages = implode( ". ", $this->getErrors() );
                    $errorMessage = "Error saving netsuite id: " . $record->{'netsuite_id.value'} . ". Error: " . $messages;
                    $this->log( $errorMessage, 'ERROR', $this->__logCategory );
                    
                    $message = null;
                    foreach ($this->getErrors() as $m) {
                        $message .= $m->getMessage() . ". ";
                    }
                    
                    $record->set( 'recent_error', true );
                    $record->set( 'recent_error_message', $message );
                    $record->set( 'recent_error_datetime', \Dsc\Mongo\Metastamp::getDate( 'now' ) );
                    $record->save();
                    
                }
            }
            catch ( \Exception $e )
            {
                $errorMessage = "Caught Error while saving netsuite id: " . $record->{'netsuite_id.value'} . ". Error: " . $e->getMessage();
                $this->log( $errorMessage, 'ERROR', $this->__logCategory );
                
                $record->set( 'recent_error', true );
                $record->set( 'recent_error_message', $e->getMessage() );
                $record->set( 'recent_error_datetime', \Dsc\Mongo\Metastamp::getDate( 'now' ) );
                $record->save();
            }
        }
        
        $time_taken = microtime( true ) - $start;
        $errorMessage = "saved " . $count . " records in " . number_format( $time_taken, 4 ) . " seconds";
        $this->log( $errorMessage, 'INFO', $this->__logCategory );
    }

    /**
     * Takes a row from the mongodb.collection
     * and exports it based on its parent status
     *
     * @param unknown $mapper            
     */
    public function exportItem( $record )
    {
        $result = false;
        
        if ((int) trim( $record->{'parent.value'} ) > 0)
        {
            /*
             * \Dsc\System::instance()->addMessage( 'child' ); $data = $this->dataArrayFromMap( $record->cast(), $this->variantFieldMap() ); $data = $this->createVariantAttributes( $data ); \Dsc\System::instance()->addMessage( \Dsc\Debug::dump($data) ); //$prefab = $this->prefabVariant()->bind($data); //\Dsc\System::instance()->addMessage( \Dsc\Debug::dump($prefab->cast()) ); return $result;
             */
            // is this record a netsuite child product?
            if ($exportResult = $this->exportVariant( $record ))
            {
                $record->set( 'shop.product_id', $exportResult['product']->get( '_id' ) );
                $record->set( 'shop.variant_id', $exportResult['variant']['id'] );
                $record->set( 'recent_error', false );
                $record->set( 'recent_error_datetime', null );               
                $record->save();
                $result = $exportResult;
            }
        }
        else
        {
            /*
             * \Dsc\System::instance()->addMessage( 'parent' ); $data = $this->dataArrayFromMap( $record->cast(), $this->productFieldMap() ); $data = $this->createProductAttributes( $data ); //\Dsc\System::instance()->addMessage( \Dsc\Debug::dump($record->cast()) ); \Dsc\System::instance()->addMessage( \Dsc\Debug::dump($data) ); return $result;
             */
            // or is it a netsuite parent parent?
            if ($exportResult = $this->exportProduct( $record ))
            {
                $record->set( 'shop.product_id', $exportResult['product']->get( '_id' ) );
                $record->set( 'recent_error', false );
                $record->set( 'recent_error_datetime', null );                
                $record->save();
                $result = $exportResult;
            }            
        }
        
        return $result;
    }

    /**
     * Takes a netsuite parent product and creates a Shop product with it
     *
     * @param unknown $record            
     */
    protected function exportProduct( $record )
    {
        $result = false;
        
        $data = $this->dataArrayFromMap( $record->cast(), $this->productFieldMap() );
        $data = $this->createProductAttributes( $data );
        
        $netsuite_id = \Dsc\ArrayHelper::get( $data, 'netsuite.id' );
        
        $isNew = true;
        $existing = $this->existingProduct( $netsuite_id );
        if (! empty( $existing->id ) && $existing->{'netsuite.id'} == $netsuite_id)
        {
            $isNew = false;
        }
        
        if ($isNew)
        {
            // Create thumbnail of image if necessary
            $image_slug = null;
            if ($amrita_image_url = \Dsc\ArrayHelper::get( $data, 'amrita.image_url' ))
            {
                $existing_asset = $this->existingAsset( $amrita_image_url );
                if (! empty( $existing_asset->{'slug'} ))
                {
                    $image_slug = $existing_asset->{'slug'};
                }
                
                if (! $image_slug)
                {
                    $new_asset = $this->createAssetFromUrl( $amrita_image_url );
                    if (! empty( $new_asset['slug'] ))
                    {
                        $image_slug = $new_asset['slug'];
                    }
                }
            }
            
            // create the product
            $product = $this->productModel();
            $product->bind( $data );
            $product->set( 'featured_image.slug', $image_slug );
            if ($product->save())
            {
                if (count($product->variants) == 1)
                {
                	if (empty($product->{'variants.0.netsuite.id'}))
                	{
                	    $product->{'variants.0.netsuite.id'} = $netsuite_id;
                	    $product->save();
                	}
                }       
                         
                $result = array(
                    'product' => $product,
                    'action' => 'create' 
                );
            }
            else
            {
                $errorMessage = "Error creating new product for netsuite id: " . $record->{'netsuite_id.value'};
                $this->log( $errorMessage, 'ERROR', $this->__logCategory );
            }
        }
        else
        {
            // load the product and update it
            // UPDATES do overwrite these properties
            // quantity
            // attributes?
            if ($quantity = \Dsc\ArrayHelper::get( $data, 'quantities.manual' ))
            {
                $existing->set( 'quantities.manual', $quantity );
                // if there is only one variant, update its quantity too
                if ($existing->variants_count == 1) 
                {
                    $existing->set( 'variants.0.quantity', $quantity );
                    
                    if (empty($existing->{'variants.0.netsuite.id'}))
                    {
                        $existing->{'variants.0.netsuite.id'} = $netsuite_id;
                    }                    
                }
            }
            
            // Are there any new PAOs for existing attributes?  If so, merge them in, but preserve the OLD attribute ID
            $new_attributes = (array) \Dsc\ArrayHelper::get( $data, 'attributes' );
            $existing_attributes = &$existing->attributes;
            
            foreach ($new_attributes as $new_attribute) 
            {
                foreach ($existing_attributes as $existing_key=>&$existing_attribute) 
                {
                    if (strtolower($existing_attribute['title']) == strtolower($new_attribute['title'])) 
            		{
            		    $new_attribute_options = (array) $new_attribute['options'];
            		    $existing_attribute_options = (array) $existing_attribute['options'];
            		    
                        foreach ($new_attribute_options as $new_attribute_option) 
                        {
                            $found = false;
                            
                        	foreach ($existing_attribute_options as &$existing_attribute_option) 
                        	{
                        	    if (strtolower($existing_attribute_option['value']) == strtolower($new_attribute_option['value'])) 
                        	    {
                        	        $found = true;
                        	    }
                        	}
                        	
                        	if (!$found) 
                        	{
                        	    $existing_attribute['options'][] = $new_attribute_option;
                        	}
                        }
            		}
                }
            	
            }
            //$existing->set( 'attributes', (array) \Dsc\ArrayHelper::get( $data, 'attributes' ) );
            
            // UPDATES must not overwrite these properties:
            // name/title
            // description
            // pricing
            // product_enabled
            
            // \Dsc\System::addMessage( \Dsc\Debug::dump( $data ) );
            // \Dsc\System::addMessage( \Dsc\Debug::dump( \Dsc\ArrayHelper::get( $data, 'title' ) ) );
            // \Dsc\System::addMessage( \Dsc\Debug::dump( $existing->cast() ) );
            
            // remove these
            //$existing->set( 'title', \Dsc\ArrayHelper::get( $data, 'title' ) );
            $existing->set( 'prices.default', (float) \Dsc\ArrayHelper::get( $data, 'prices.default' ) );
            $existing->set( 'prices.wholesale', (float) \Dsc\ArrayHelper::get( $data, 'prices.wholesale' ) );
            $existing->set( 'prices.list', (float) \Dsc\ArrayHelper::get( $data, 'prices.list' ) );
            
            // Create thumbnail of image if necessary
            /*
            $image_slug = null;
            if ($amrita_image_url = \Dsc\ArrayHelper::get( $data, 'amrita.image_url' ))
            {
                $existing_asset = $this->existingAsset( $amrita_image_url );
                if (! empty( $existing_asset->{'slug'} ))
                {
                    $image_slug = $existing_asset->{'slug'};
                }
                
                if (! $image_slug)
                {
                    $new_asset = $this->createAssetFromUrl( $amrita_image_url );
                    if (! empty( $new_asset['slug'] ))
                    {
                        $image_slug = $new_asset['slug'];
                    }
                }
                
                $existing->set( 'featured_image.slug', $image_slug );
            }
            */
            
            $existing->save();
            
            $result = array(
                'product' => $existing,
                'action' => 'update' 
            );
        }
        
        return $result;
    }

    /**
     * Takes a netsuite matrix product and creates a Shop variant with it
     *
     * @param unknown $record            
     */
    protected function exportVariant( $record )
    {
        $result = false;
        
        // does a record for the parent exist? if not, create it
        $parent = $this->existingProduct( $record->{'parent.value'} );
        if (empty( $parent->id ))
        {
            $parent_record = (new \Netsuite\Models\Import\Products)->load( array(
                'netsuite_id.value' => $record->{'parent.value'} 
            ) );
            
            if (empty( $parent_record->id ))
            {
                $this->setError( "No parent record could be found" );
                $record->set( 'no_parent_found', true );
                $record->set( 'do_not_import', true );
                $record->save();
                return false;
            }
            
            // load the $parent_record, then run it through exportProduct( $parent_record )
            // if that succeeds, then create the variant
            $parent_result = $this->exportProduct( $parent_record );
            if (empty( $parent_result['product']->id ))
            {
                $errorMessage = "Error creating parent product (#" . $parent_record->{'netsuite_id.value'} . ") for netsuite id: " . $record->{'netsuite_id.value'};
                $this->log( $errorMessage, 'ERROR', $this->__logCategory );
                return false;
            }
            $parent = $parent_result['product'];
        }
        
        // is this a new variant or an update?
        $isNew = true;
        // does the parent have a variant for this netsuite_id?
        $data = $this->dataArrayFromMap( $record->cast(), $this->variantFieldMap() );
        $netsuite_id = \Dsc\ArrayHelper::get( $data, 'netsuite.id' );
        $existing = $this->findVariant( $netsuite_id, $parent );
        
        if (! empty( $existing['id'] ))
        {
            $isNew = false;
        }
        
        if ($isNew)
        {
            // Create thumbnail of image if necessary
            $image_slug = null;
            if ($amrita_image_url = \Dsc\ArrayHelper::get( $data, 'amrita.image_url' ))
            {
                $existing_asset = $this->existingAsset( $amrita_image_url );
                if (! empty( $existing_asset->{'slug'} ))
                {
                    $image_slug = $existing_asset->{'slug'};
                }
                
                if (! $image_slug)
                {
                    $new_asset = $this->createAssetFromUrl( $amrita_image_url );
                    if (! empty( $new_asset['slug'] ))
                    {
                        $image_slug = $new_asset['slug'];
                    }
                }
            }
            
            // create the variant
            $data = $this->createVariantAttributes( $data );
            $prefab = $this->prefabVariant()->bind( $data );
            $prefab->set( 'image', $image_slug );
            
            $key_values = json_decode( $prefab->attributes );
            if (empty( $key_values ))
            {
                // TODO Export the parent, then try again
                // this will create the attribue options in the parent
                $parent_record = (new \Netsuite\Models\Import\Products)->load( array(
                    'netsuite_id.value' => $record->{'parent.value'}
                ) );
                
                if (empty( $parent_record->id ))
                {
                    $this->setError( "No parent record could be found" );
                    $record->set( 'no_parent_found', true );
                    $record->set( 'do_not_import', true );
                    $record->save();
                    return false;
                }
                
                // load the $parent_record, then run it through exportProduct( $parent_record )
                // if that succeeds, then create the variant
                $parent_result = $this->exportProduct( $parent_record );
                if (empty( $parent_result['product']->id ))
                {
                    $errorMessage = "Error creating parent product (#" . $parent_record->{'netsuite_id.value'} . ") for netsuite id: " . $record->{'netsuite_id.value'};
                    $this->log( $errorMessage, 'ERROR', $this->__logCategory );
                    return false;
                }
                $parent = $parent_result['product'];

                /**
                 * NOW try creating the variant again
                 */
                $data = $this->createVariantAttributes( $data );
                $prefab = $this->prefabVariant()->bind( $data );
                $prefab->set( 'image', $image_slug );
                
                $key_values = json_decode( $prefab->attributes );
                if (empty( $key_values ))
                {
                    $this->setError( "Could not create the variant for netsuite id: " . $record->{'netsuite_id.value'} . " because the variant's attributes are empty." );
                    $record->set( 'do_not_import', true );
                    $record->save();
                    return false;                    
                }
            }
            
            $variants = (array) $parent->variants;
            sort( $key_values );
            $sorted_key = implode( '-', $key_values );
            
            $found = false;
            foreach ($variants as $vkey=>$variant) 
            {
                if ($variant['key'] == (string) $sorted_key) {
                    $prefab->id = $variant['id'];
                    $prefab->key = (string) $sorted_key;
                    $variants[$vkey] = $prefab->cast();
                    $found = true;
                }
            }
            
            if (!$found) {
                $variants[] = $prefab->cast();
            }
            
            $parent->set( 'variants', $variants );
            
            // SAVE THE VARIANT
            if ($parent = $parent->save())
            {
                $result = array(
                    'product' => $parent,
                    'variant' => $prefab,
                    'action' => 'create' 
                );
            }
            else
            {
                $this->setError( "Could not create the variant for netsuite id: " . $record->{'netsuite_id.value'} );
            }
        }
        else
        {
            // load the variant and update it
            // UPDATES do overwrite these properties for the variant
            // quantity
            $quantity = \Dsc\ArrayHelper::get( $data, 'quantity' );
            $existing['quantity'] = $quantity;
            
            $price = \Dsc\ArrayHelper::get( $data, 'prices.default' );
            $existing['price'] = (float) $price;

            // re-enable the variant, but only if the start_date is <= today
            // and quantity > 0 
            if (empty($existing['start_date']) || $existing['start_date'] <= date('Y-m-d') ) 
            {
                if ($existing['quantity'] > 0) 
                {
                    $existing['enabled'] = 1;
                    
                    // since we're re-enabling a variant, check whether the parent product should be re-published
                    if ($parent->published(false) && $parent->{'publication.status'} != 'published') 
                    {
                    	$parent->{'publication.status'} = 'published';
                    }
                }                
            }            
            
            // UPDATES must not overwrite these properties:
            // name/title
            // description
            // pricing

            $sorted_key = $existing['key'];
            $variants = (array) $parent->variants;
            
            $found = false;
            foreach ($variants as $vkey=>$variant)
            {
                if ($variant['key'] == (string) $sorted_key) {
                    $variants[$vkey] = $existing;
                    $found = true;
                }
            }
            
            if (!$found) {
                $variants[] = $existing;
            }
            
            $parent->set( 'variants', $variants );
            
            // SAVE THE VARIANT
            if ($parent = $parent->save())
            {
                $result = array(
                    'product' => $parent,
                    'variant' => $existing,
                    'action' => 'update' 
                );
            }
            else
            {
                $this->setError( "Could not create the variant for netsuite id: " . $record->{'netsuite_id.value'} );
            }            

        }
        
        return $result;
    }

    /**
     * Creates a field relationship array
     * between the \Shop\Prefabs\Product object and the \Netsuite\Mappers\ImportProducts object.
     *
     * TODO Enable this to be extended by a simple ini file
     */
    protected function productFieldMap()
    {
        $return = array(
            'title' => array(
                'display_name.value',
                'name.value' 
            ),
            'copy' => array(
                'description.value' 
            ),
            'netsuite.id' => array(
                'netsuite_id.value' 
            ),
            'netsuite.recordtype' => array(
                'recordtype' 
            ),
            'netsuite.parent' => array(
                'parent.value' 
            ),
            'netsuite.last_modified' => array(
                'last_modified_in_netsuite.value' 
            ),
            'netsuite.class' => array(
                'class.column_value.name' 
            ),
            'netsuite.department' => array(
                'department.column_value.name' 
            ),
            'netsuite.currency' => array(
                'currency.value' 
            ),
            'tracking.sku' => array(
                'name.value' 
            ),
            'prices.default' => array(
                'online_price.value' 
            ),
            'prices.wholesale' => array(
                'wholesale_price.value' 
            ),
            'prices.list' => array(
                'list_price.value',
                'list_price.column_value' 
            ),
            'quantities.manual' => array(
                'quantity.value' 
            ),
            'amrita.image_url' => array(
                'image_url.value',
                'other_image_url.value' 
            ),
            'amrita.type' => array(
                'type.column_value.name' 
            ),
            'amrita.material' => array(
                'product_material.column_value.name' 
            ),
            'amrita.necklace_closure' => array(
                'necklace_closure.value' 
            ),
            'amrita.measurements' => array(
                'dimensions.value' 
            ),
            'amrita.color_dual_tone' => array(
                'color_dual_tone.value' 
            ),
            // attributes
            'amrita.size' => array(
                'size.column_value' 
            ),
            'amrita.colors' => array(
                'color.column_value' 
            ),
            'amrita.earring_closure' => array(
                'earring_closure.column_value' 
            ),
            'amrita.plating' => array(
                'plating.column_value' 
            ) 
        );
        
        return $return;
    }

    /**
     * Creates a field relationship array
     * between the \Shop\Prefabs\Variant object and the \Netsuite\Mappers\ImportProducts object.
     *
     * TODO Enable this to be extended by a simple ini file
     */
    protected function variantFieldMap()
    {
        $return = array(
            'title' => array(
                'display_name.value' 
            ),
            'sku' => array(
                'name.value' 
            ),
            'price' => array(
                'online_price.value' 
            ),
            'prices.default' => array(
                'online_price.value' 
            ),
            'prices.wholesale' => array(
                'wholesale_price.value' 
            ),
            'prices.list' => array(
                'list_price.value',
                'list_price.column_value' 
            ),
            'quantity' => array(
                'quantity.value' 
            ),
            'netsuite.id' => array(
                'netsuite_id.value' 
            ),
            'netsuite.recordtype' => array(
                'recordtype' 
            ),
            'netsuite.parent' => array(
                'parent.value' 
            ),
            'netsuite.last_modified' => array(
                'last_modified_in_netsuite.value' 
            ),
            'netsuite.class' => array(
                'class.column_value.name' 
            ),
            'netsuite.department' => array(
                'department.column_value.name' 
            ),
            'netsuite.currency' => array(
                'currency.value' 
            ),
            'amrita.type' => array(
                'type.column_value.name' 
            ),
            'amrita.image_url' => array(
                'image_url.value',
                'other_image_url.value' 
            ),
            'amrita.material' => array(
                'product_material.column_value.name' 
            ),
            'amrita.necklace_closure' => array(
                'necklace_closure.value' 
            ),
            'amrita.measurements' => array(
                'dimensions.value' 
            ),
            'amrita.color_dual_tone' => array(
                'color_dual_tone.value' 
            ),
            // attributes
            'amrita.colors' => array(
                'color.column_value.name',
                'color.value' 
            ),
            'amrita.size' => array(
                'size.column_value.name',
                'size.value' 
            ),
            'amrita.earring_closure' => array(
                'earring_closure.column_value.name',
                'earring_closure.value' 
            ),
            'amrita.plating' => array(
                'plating.column_value.name',
                'plating.value' 
            ) 
        );
        
        return $return;
    }

    protected function createProductAttributes( $data )
    {
        // foreach of the following values, create an attribute in the $data array
        /*
         * 'amrita.colors' => array( 'color.column_value.name', 'color.column_value' ), 'amrita.size' => array( 'size.value' ), 'amrita.earring_closure' => array( 'earring_closure.value' ), 'amrita.plating'
         */
        $attributes = array();
        
        $attribute_keys = array(
            'amrita.colors' => 'Color',
            'amrita.size' => 'Size',
            'amrita.earring_closure' => 'Earring Closure',
            'amrita.plating' => 'Plating' 
        );
        
        foreach ( $attribute_keys as $attribute_key => $attribute_key_name )
        {
            $items = \Dsc\ArrayHelper::get( $data, $attribute_key );
            
            // correct for borked data -- thanks Netsuite!
            if (is_array( $items ) && ! empty( $items['name'] ) && ! empty( $items['internalid'] ))
            {
                $items = array(
                    $items 
                );
            }
            
            if (! empty( $items ) && is_array( $items ))
            {
                $options = array();
                foreach ( $items as $item )
                {
                    if (empty( $item ) || empty( $item['name'] ) || empty( $item['internalid'] ))
                    {
                        continue;
                    }
                    
                    $options[] = $this->prefabAttributeOption()->bind( array(
                        'netsuite.id' => trim( $item['internalid'] ),
                        'value' => trim( $item['name'] ) 
                    ) )->cast();
                }
                
                if (! empty( $options ))
                {
                    $attr = $this->prefabAttribute()->bind( array(
                        'id' => (string) new \MongoId(),
                        'title' => $attribute_key_name,
                        'options' => $options 
                    ) )->cast();
                    
                    $attributes[] = $attr;
                }
            }
        }
        
        $data['attributes'] = $attributes;
        
        return $data;
    }

    protected function createVariantAttributes( $data )
    {
        // foreach of the following values, create an attribute in the $data array
        /*
         * 'amrita.colors' => array( 'color.column_value.name', 'color.column_value' ), 'amrita.size' => array( 'size.value' ), 'amrita.earring_closure' => array( 'earring_closure.value' ), 'amrita.plating'
         */
        $attributes = array();
        
        $attribute_keys = array(
            'amrita.colors' => 'Color',
            'amrita.size' => 'Size',
            'amrita.earring_closure' => 'Earring Closure',
            'amrita.plating' => 'Plating' 
        );
        
        $netsuite_id = \Dsc\ArrayHelper::get( $data, 'netsuite.parent' );
        
        foreach ( $attribute_keys as $attribute_key => $attribute_key_name )
        {
            $item = trim( (string) \Dsc\ArrayHelper::get( $data, $attribute_key ) );
            if (! empty( $item ))
            {
                // Find the MongoId for this option where the product_id = the parent
                $pao_id = null;
                if ($pao = $this->findProductAttributeOption( $netsuite_id, $attribute_key_name, $item ))
                {
                    $pao_id = $pao['id'];
                }
                
                if ($pao_id)
                {
                    $attributes[] = (string) $pao_id;
                }
            }
        }
        
        $data['attributes'] = json_encode( $attributes );
        
        return $data;
    }

    public function findProductAttributeOption( $netsuite_id, $attribute_title, $pao_value )
    {
        // Find the attributes.options (\Shop\Prefabs\AttributeOption object)
        // where the product.netsuite.id = X and attributes.options.$.value = pao_value
        $result = false;
        
        $mapper = $this->existingProduct( $netsuite_id );
        if (empty( $mapper->id ))
        {
            return $result;
        }
        
        // give me all the attributes.options where attribute.title == $attribute_title
        $paos = \Dsc\ArrayHelper::where( $mapper->attributes, function ( $key, $pa ) use($attribute_title )
        {
            if (! empty( $pa['title'] ) && $pa['title'] == $attribute_title && ! empty( $pa['options'] ))
            {
                return $pa['options'];
            }
        } );
        
        try
        {
            $paos = \Dsc\ArrayHelper::level( $paos, 1 );
        }
        catch ( \Exception $e )
        {
            return $result;
        }
        
        if (empty( $paos ))
        {
            return $result;
        }
        
        $filtered = \Dsc\ArrayHelper::where( $paos, function ( $key, $pao ) use($pao_value )
        {
            if (! empty( $pao['value'] ) && $pao['value'] == $pao_value && ! empty( $pao['id'] ))
            {
                return $pao;
            }
        } );
        
        if (! empty( $filtered ))
        {
            $result = $filtered[0];
        }
        
        return $result;
    }

    /**
     * Finds the Variant for the corresponding Netsuite ID
     *
     * @param unknown $netsuite_id            
     * @param unknown $parent_mapper            
     */
    public function findVariant( $netsuite_id, $parent_mapper = null )
    {
        $result = false;
        
        if (empty( $parent_mapper->id ))
        {
            $parent_mapper = $this->existingVariant( $netsuite_id );
            if (empty( $parent_mapper->id ))
            {
                return false;
            }
        }
        
        // give me all the variants in the parent_mapper whose netsuite.id = $netsuite_id
        $filtered = \Dsc\ArrayHelper::where( $parent_mapper->variants, function ( $key, $variant ) use($netsuite_id )
        {
            if (! empty( $variant['netsuite']['id'] ) && $variant['netsuite']['id'] == $netsuite_id)
            {
                return $variant;
            }
        } );
        
        if (! empty( $filtered ))
        {
            $result = $filtered[0];
        }
        
        // TODO Find based on attributes
        
        return $result;
    }

    protected function dataArrayFromMap( $source, $fieldmap )
    {
        $array = array();
        
        foreach ( $fieldmap as $key => $path_array )
        {
            // if $path is an array, keep trying each potential value until one is found. fieldMap() prioritizes them
            foreach ( $path_array as $path )
            {
                if (\Dsc\ArrayHelper::exists( $source, $path ))
                {
                    $value = \Dsc\ArrayHelper::get( $source, $path );
                    if ($value)
                    {
                        $array[$key] = $value;
                        break;
                    }
                }
                /*
                 * $explode_path = explode('.', $path); $count_explode = count($explode_path); $result = $source; for ($i=0; $i<$count_explode; $i++) { if (isset($result[$explode_path[$i]])) { $result = $result[$explode_path[$i]]; } else { continue; } } // if we got here, then we found a match $array[$key] = $result; break; // break the [foreach $path_array] loop
                 */
            }
        }
        
        return $array;
    }

    protected function prefabProduct()
    {
        $prefab = new \Shop\Models\Products;
        
        return $prefab;
    }

    protected function prefabVariant()
    {
        // TODO Allow this to be overridden in settings
        $prefab = new \Amrita\Prefabs\Variant;
        
        return $prefab;
    }

    protected function prefabAttribute()
    {
        // TODO Allow this to be overridden in settings
        $prefab = new \Amrita\Prefabs\Attribute;
        
        return $prefab;
    }

    protected function prefabAttributeOption()
    {
        // TODO Allow this to be overridden in settings
        $prefab = new \Amrita\Prefabs\AttributeOption;
        
        return $prefab;
    }

    /**
     * Finds the Shop product for a netsuite product ID
     *
     * @param unknown $netsuite_id            
     */
    public function existingProduct( $netsuite_id )
    {
        return $this->productModel()->load( array(
            'netsuite.id' => $netsuite_id 
        ) );
    }

    /**
     * Finds the Shop product that contains the variant for a netsuite product ID
     *
     * @param unknown $netsuite_id            
     */
    public function existingVariant( $netsuite_id )
    {
        return $this->productModel()->load( array(
            'variants.netsuite.id' => $netsuite_id 
        ) );
    }

    protected function productModel()
    {
        $model = new \Shop\Models\Products;
        return $model;
    }

    /**
     * Create a Shop assets and put it on S3
     * 
     * @param unknown $url
     * @return boolean
     */
    public function createAssetFromUrl( $url )
    {
        try {
            $asset = \Shop\Models\Assets::createFromUrlToS3( $url, array(
                'width' => 460,
                'height' => 308
            ) );
        }
        catch (\Exception $e) {
        	$asset = false;
        }
        
        return $asset;
    }

    /**
     * Finds the Shop asset for a url
     *
     * @param unknown $netsuite_id            
     */
    public function existingAsset( $url )
    {
        return (new \Shop\Models\Assets)->load( array(
            'source_url' => $url 
        ) );
    }

    public function prepareProductData( $record ) 
    {
        $data = $this->dataArrayFromMap( $record->cast(), $this->productFieldMap() );
        $data = $this->createProductAttributes( $data );
        
        return $data;
    }
    
    public function prepareVariantData( $record ) 
    {
        $data = $this->dataArrayFromMap( $record->cast(), $this->variantFieldMap() );
        
        return $data;
    }
}