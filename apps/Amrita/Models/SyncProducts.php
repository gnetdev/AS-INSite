<?php
namespace Amrita\Models;

class SyncProducts extends \Dsc\Mongo\Collection
{
	protected $__type = 'amrita.product-sync';
	protected $__pageSize = 50;
	protected $__max_loops = 10;
	protected $__logCategory = 'SyncProduct';
	
	protected $__last_mode = 3; // 1 = sync products and categories, 2 = sync related products
	protected $__sync_site_url = 'https://amritasingh.com';
	
	private $__sync_db = null;
	
	protected function getSyncDbConnection(){
		$db_server = 'mongodb://india:asin113!PM@amritasingh.co.in:27017/india';
		$db_name = 'india';
		
		if( $this->__sync_db == null ){
			$this->__sync_db = new \MongoDB( new \MongoClient($db_server), $db_name);
		}

		return $this->__sync_db;
	}
	
	public function run()
	{
		if (!$this->canRun()) {
			return false;
		}
		if ($this->isRunning()) {
			return $this->continueRunning();
		}
		return $this->startRunning();
	}
	
	public function getCurrentStatus( $prefix=null )
	{
		$this_model_class = strtolower( get_class($this) );
		$config_name = str_replace( '\\', '-', $this_model_class . $prefix );
	
		$model = new \Dsc\Mongo\Collections\Settings;
		$model->load(array('type' => $this->__type, 'name' => $config_name ));
	
		$currentStatus = new \Amrita\Models\Prefabs\CurrentStatus( $model->get('current_status') );
		if (empty($currentStatus)) {
			$currentStatus = $this->getCurrentStatusObject();
		}
	
		return $currentStatus;
	}
	
	public function setCurrentStatus( $object, $prefix=null )
	{
		$currentStatus = $this->getCurrentStatus( $prefix );
		foreach ((array) $object as $key => $value) {
			if (!empty($key)) {
				$currentStatus->set($key, $value);
			}
		}
	
		$this_model_class = strtolower( get_class($this) );
		$config_name = str_replace( '\\', '-', $this_model_class . $prefix );
	
		$model = new \Dsc\Mongo\Collections\Settings;
		$model->load(array('type' => $this->__type, 'name' => $config_name ));
		$model->set('name', $config_name);
		$model->set('type', $this->__type);
		$model->set('current_status', $currentStatus->cast());
		$model->save();
	
		return $this;
	}
	
	public function resetCurrentStatus($prefix=null)
	{
		$currentStatus = $this->getCurrentStatusObject();
	
		$this_model_class = strtolower( get_class($this) );
		$config_name = str_replace( '\\', '-', $this_model_class . $prefix );
	
		$model = new \Dsc\Mongo\Collections\Settings;
		$model->load(array('type' => $this->__type, 'name' => $config_name ));
		$model->set('name', $config_name);
		$model->set('type', $this->__type);
		$model->set('current_status', $currentStatus->cast());
		$model->save();
	
		return $this;
	}
	
	public function getCurrentStatusObject()
	{
		$return = new \Amrita\Models\Prefabs\CurrentStatus;
		return $return;
	}
	
	protected function canRun()
	{
	    if (! $this->isExportComplete())
	    {
	        return false;
	    }
	    	    
		if ($this->isSyncComplete())
        {
            return false;
        }
        
        $currentStatus = $this->getCurrentStatus();
        
        // If the previous process is finished and it was last finished yesterday, run it again from the very beginning (reset current status)
        if (! empty( $currentStatus->is_finished ) && (date( 'Y-m-d', $currentStatus->{'finished.time'} ) <= date( 'Y-m-d', strtotime( 'yesterday' ) )))
        {
            $this->log( "Restarting the product synchronization.", 'INFO', $this->__logCategory );
            $this->resetCurrentStatus();
            
            return true;
        }
        
        // else If the process is finished and it was finished today, don't clutter the logs. don't do anything until tomorrow
        elseif (! empty( $currentStatus->is_finished ) && (date( 'Y-m-d', $currentStatus->{'finished.time'} ) == date( 'Y-m-d', strtotime( 'now' ) )))
        {
            return false;
        }
        
	    // else If the import is finished, mark it as finished
        elseif (!empty($currentStatus->total_items) && (int) ($currentStatus->current_page) == (int) $currentStatus->total_pages)
        {
        	if( $currentStatus->mode == $this->__last_mode ) { // really end this
        		$currentStatus->set( 'is_finished', true );
        		$currentStatus->set( 'finished', \Dsc\Mongo\Metastamp::getDate( 'now' ) );
        		$this->setCurrentStatus( $currentStatus->cast() );
        		$this->log( 'Product synchronization is finished for today.', 'INFO', $this->__logCategory );
        		
        		return false;
        	} else {
        		$currentStatus->set( 'mode', $currentStatus->mode + 1 );
        		$currentStatus->set( 'current_page', 0 );
        		$this->setCurrentStatus( $currentStatus->cast() );
        		return true;
        	}
        }        
                
        return true;
	}
	
	protected function isRunning()
	{
		$currentStatus = $this->getCurrentStatus();
    
        if (! empty( $currentStatus->total_items ) && empty( $currentStatus->is_finished ))
        {
            return true;
        }
    
        return false;
	}
	
	protected function startRunning()
	{
        $this->log( 'Synchronization Products.', 'INFO', $this->__logCategory );
		// Here, mark is_processing = true in the $currentStatus object and save it
		$currentStatus = $this->getCurrentStatusObject();
		$currentStatus->set( 'is_processing', true );
		$currentStatus->set( 'mode', 1 );
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
				$categories  = array();
				$this->syncRecords( $searchResponse->items , $categories);
				$currentStatus->set( 'current_page', $currentStatus->current_page + 1 );
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
	
	protected function fetchRecords($offset = null, $limit = null)
	{
		$model = new \Shop\Models\Products;
		
		if (empty($limit)) {
			$limit = $this->__pageSize;
		}
		$model->setState( 'list.limit', $limit );
		
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
		}
		
		return $searchResponse;
	}
	
	protected function fetchSyncRecords($offset = null, $limit = null)
	{
		$currentStatus = $this->getCurrentStatus();
		$db_2 = $this->getSyncDbConnection();
		$products_collection = $db_2->selectCollection( 'shop.products' );

		$cursor = $products_collection->find();
		if (empty($limit)) {
			$limit = $this->__pageSize;
		}
		$cursor->limit( $limit );
		
		if (!empty( $offset ) ) {
			$cursor->skip( $offset * $this->__pageSize );
		}
		
		$total = $products_collection->count();
		$searchResponse = new \Dsc\Pagination( $total, $this->__pageSize );
		$searchResponse->items = array();
		
	    foreach ($cursor as $doc) {
        	$item = new \Dsc\Mongo\Collection( $doc );
        	$searchResponse->items []= $item;
        }           
				
		if (! empty( $searchResponse ))
		{
			if (!empty($offset))
			{
				$searchResponse->setCurrent($offset+1);
			}
		}
		
		return $searchResponse;
	}
	
	protected function continueRunning()
	{
		// maps for known IDs
		$cached_data  = array();
		for($i = 1; $i <= $this->__max_loops; $i ++)
		{
			set_time_limit( 0 );
		
            $currentStatus = $this->getCurrentStatus();
			$currentStatus->set( 'is_processing', true );
        	$this->setCurrentStatus( $currentStatus->cast() );
			$searchResponse = null;
			switch( $currentStatus->mode ){
				case 1 :
					$searchResponse = $this->fetchRecords( $currentStatus->current_page, $currentStatus->items_per_page );
					break;
				case 2 :
					$searchResponse = $this->fetchSyncRecords( $currentStatus->current_page, $currentStatus->items_per_page );
					break;
			}
			if (empty( $searchResponse ))
			{
				$return = false;
		
				$errorMessage = "No more products to sync";
				$this->log( $errorMessage, 'ERROR', $this->__logCategory );

                $currentStatus->set( 'is_processing', false );
                if( $currentStatus->mode == $this->__last_mode ){
                	$currentStatus->set( 'is_finished', true );
                	$currentStatus->set( 'finished', \Dsc\Mongo\Metastamp::getDate( 'now' ) );
                }
				$this->setCurrentStatus( $currentStatus->cast() );
			}
			else
			{
				$return = true;
				try
		        {
		        	$clone = clone $searchResponse;
		            unset( $clone->items );
					$currentStatus->bind( $clone );
					$this->setCurrentStatus( $currentStatus->cast() );
				}
		        catch ( \Exception $e )
		        {
		        	$errorMessage = $e->getMessage();
					$this->log( $errorMessage, 'ERROR', $this->__logCategory );
				}

				$errorMessage = "SyncProducts found " . $searchResponse->total_items . " records, but is only processing " . $searchResponse->items_per_page . " starting with " . (int) ( $currentStatus->current_page) * $searchResponse->items_per_page;
		        $this->log( $errorMessage, 'INFO', $this->__logCategory );
		
				try
	            {
	            	switch( $currentStatus->mode ){
	            		case 1: // sync products and categories
	            			{
								$this->syncRecords( $searchResponse->items );
	            				break;
	            			}
	            		case 2: // sync related products
	            			{
								$this->syncRelatedProducts( $searchResponse->items, $cached_data);
	            				break;
	            			}
	            	}
                	$currentStatus->set( 'is_processing', false );
	               	$this->setCurrentStatus( $currentStatus->cast() );                
	            }
		        catch ( \Exception $e )
		        {
		        	$errorMessage = $e->getMessage();
		            $this->log( $errorMessage, 'ERROR', $this->__logCategory );
				}
			}
		}
	
		return $return;
	}
	
	/**
	 * Checks if the \Netsuite\Models\Export\ShopProducts is complete
	 * ON THE OTHER (Indian) SERVER
	 *
	 * @return boolean
	 */
	public function isExportComplete()
	{
	    $exporter = new \Netsuite\Models\Export\ShopProducts();
	    
	    $db_2 = $this->getSyncDbConnection();
	    $settings_collection = $db_2->selectCollection( 'common.settings' );
	    $doc = $settings_collection->findOne( array(
	        'type' => $exporter->type(),
	        'name' => 'netsuite-models-export-shopproducts'
	    ) );
	    
	    if (empty($doc)) 
	    {
	        return false;
	    }
	    	    
	    $model = new \Dsc\Mongo\Collections\Settings();
        $model->bind($doc);
        
	    $currentStatus = new \Netsuite\Models\Prefabs\CurrentStatus( $model->get( 'current_status' ) );
	    if (! empty( $currentStatus->is_finished ) && (date( 'Y-m-d', $currentStatus->{'finished.time'} ) == date( 'Y-m-d', strtotime( 'now' ) )))
	    {
	        return true;
	    }
	
	    return false;
	}

    /**
     * Checks if the synchronisation between DBs is complete
     *
     * @return boolean
     */
    public function isSyncComplete()
    {
        $model = new \Dsc\Mongo\Collections\Settings();
        $model->load( array(
            'type' => $this->__type,
        ) );
        $currentStatus = new \Amrita\Models\Prefabs\CurrentStatus( $model->get( 'current_status' ) );
        if (! empty( $currentStatus->is_finished ) && (date( 'Y-m-d', $currentStatus->{'finished.time'} ) == date( 'Y-m-d', strtotime( 'now' ) )))
        {
            return true;
        }
        return false;
    }
    
    protected function syncRecords(array $records ){
    	$db_2 = $this->getSyncDbConnection();
    	$products_collection = $db_2->selectCollection( 'shop.products' );
    	$categories_collection = $db_2->selectCollection( 'common.categories' );
    	$assets_collection = $db_2->selectCollection( 'common.assets.files' );
    	 
    	$i = 0;
    	$update = false;
    	foreach( $records as $record ){
    		
    		// let's see, if we can find this product in the other db
    		$other_product_cursor = $products_collection->find( array( 'slug' => $record->{'slug'} ) );
    		
    		$other_product_cursor->limit(1);
    		$other_product_cursor->skip(0);
    		
    		$other_product = null;
    		$update = false;
    		// found it so load it!
    		if ($other_product_cursor->hasNext()) {
    			$other_product = $other_product_cursor->getNext();
    			$update = true;
    		} else {
    			// didnt found it so add it    			
    			$other_product = $record->cast();
    			unset( $other_product['_id'] ); 
    		}
    		
    		// just in case some old-dog shows up among products
    	    if( !isset($other_product['categories']) ) {
    			$other_product['categories'] = array();
    		}
    		
    		// ok, now let me fix the dependencies like IDs
    		
    		// first the easy stuff, categories
    		for( $idx = 0, $c = count( $other_product['categories'] ); $idx < $c; $idx++ ){
 				$cat =  $other_product['categories'][$idx];
    			$cat_cursor = $categories_collection->find( array( 'slug' => $cat['slug'], 'type' => 'shop.categories' ) );
    			$cat_cursor->limit(1);
    			$cat_cursor->skip(0);
    			
    			$other_cat = null;
    			// found it so load it!
    			if ($cat_cursor->hasNext()) {
    				$other_cat = $cat_cursor ->getNext();
    			} else {
    				// didnt found it so add it
    				$cat_model = (new \Shop\Models\Categories())->setState( 'filter.slug', $cat['slug'] )->getItem();
    				if( $cat_model != null ){
    					$other_cat = $cat_model->cast();
    					
    					unset( $other_cat['_id'] );
    					$categories_collection->insert(
    							$other_cat,
    							array( 'w' => 0 )
    					);
    				}
    			}
    			
    			if( $other_cat == null ){
    				unset( $other_product['categories'][$idx] );
    			} else {
    				$other_product['categories'][$idx]['id'] = new \MongoId( (string)$other_cat['_id'] );
    			}
    		}
    		
    		$other_product['categories'] = array_values( $other_product['categories'] ); // in case some categories were deleted as non-existing
    		
    		//then, the tough part, assets
    		if( !empty( $record->{'featured_image.slug'} ) ){
    			// migrate featured image, if you have to
    			
    			$img_cursor = $assets_collection->find( array( 'slug' => $record->{'featured_image.slug'}, 'type' => 'shop.assets' ) );
    			$img_cursor->limit(1);
    			$img_cursor->skip(0);
    			 
    			// didnt find it so we need to move it over
    			if (! $img_cursor->hasNext()) {
    				$this->migrateAsset( $record->{'featured_image.slug'} );
    			}
    		}
    		
    		// migrate other images, if you have to
    		if( !empty( $record->{'images'} ) ){
    			foreach( (array)$record->{'images'} as $img ){
    				$img_cursor = $assets_collection->find( array( 'slug' => $img['image'], 'type' => 'shop.assets' ) );
    				$img_cursor->limit(1);
    				$img_cursor->skip(0);
    				
    				// didnt find it so we need to move it over
    				if (! $img_cursor->hasNext()) {
	    				$this->migrateAsset( $img['image'] );
    				}
    			}
    		}
    		
    		// save the current product
    		if( $update ){
    		    // NOTE: only sync these fields on update title, description, images, categories, tags
    		    $other_product['title'] = $record->title;
    		    $other_product['description'] = $record->description;
    		    $other_product['tags'] = $record->tags;
    		    
    			$products_collection->update(
    					array( '_id' => new \MongoId( $other_product['_id'] ) ),
    					$other_product,
    					array( 'multi' => false )
    			);
    		} else {
    		    // Make sure the product is unpublished
    		    $other_product['publication']['status'] = 'unpublished';
    		    
    			unset( $other_product['_id'] );
    			$products_collection->insert(
    					$other_product,
    					array( 'w' => 0 )
    			);
    		}
    	}
    }
    
    protected function syncRelatedProducts(array $records, array &$cached ){
    	$db_2 = $this->getSyncDbConnection();
    	$products_collection = $db_2->selectCollection( 'shop.products' );
    
    	$i = 0;
    	$update = false;
    	foreach( $records as $record ){
    		
    		$related_products = (array)$record->{'related_products'};
    		
    		if( !empty( $related_products ) ){
    			
    			for( $idx = 0, $c = count( $related_products ); $idx < $c; $idx++ ){
    				$val = $related_products[$idx];
    				
    				if( isset( $cached[(string)$val] ) ) { // already cached?
    					$record->{'related_products.'.$idx} = $cached[(string)$val];
    				} else { // not cached, so find this product
    					$orig_rel_product = (new \Shop\Models\Products)->setState('filter.id',  (string)$val)->getItem();
    					if( $orig_rel_product == null ){
    						unset( $related_products[$idx] );
    						continue;
    					}
    					$sync_rel_prooduct_cursor = $products_collection->find( array( 'slug' => $orig_rel_product->{'slug'} ) );
    					
    					$sync_rel_prooduct_cursor->limit(1);
    					$sync_rel_prooduct_cursor->skip(0);
    					 
    					$sync_rel_prooduct  = null;
    					// found it so load it!
    					if ($sync_rel_prooduct_cursor->hasNext()) {
    						$sync_rel_prooduct = $sync_rel_prooduct_cursor->getNext();
    					}

    					if( $sync_rel_prooduct == null ){
    						unset( $related_products[$idx] );
    					} else {
    						$related_products[$idx] = $sync_rel_prooduct['_id'];
    						$cached[(string)$val] = $sync_rel_prooduct['_id'];
    					}
    				}
    			}
    			$record->{'related_products'} = array_values( $related_products );
    			
    			$products_collection->update(
    					array( '_id' => new \MongoId( $record->id ) ),
    					$record->cast(),
    					array( 'multi' => false )
    			);
    			 
    		}
    	}
    }

    protected function migrateAsset( $slug ){
    	$db_2 = $this->getSyncDbConnection();
    	$assets_collection = $db_2->selectCollection( 'common.assets.files' );
    	$app = \Base::instance();
    	$web = \Web::instance();
    	$options = array(
    			'clientPrivateKey' => $app->get('aws.clientPrivateKey'),
    			'serverPublicKey' => $app->get('aws.serverPublicKey'),
    			'serverPrivateKey' => $app->get('aws.serverPrivateKey'),
    			'expectedBucketName' => $app->get('aws.bucketname'),
    			'expectedMaxSize' => $app->get('aws.maxsize'),
    			'cors_origin' => $app->get('SCHEME') . "://" . $app->get('HOST') . $app->get('BASE')
    	);
    	
    	if (!class_exists('\Aws\S3\S3Client')
    	|| empty($options['clientPrivateKey'])
    	|| empty($options['serverPublicKey'])
    	|| empty($options['serverPrivateKey'])
    	|| empty($options['expectedBucketName'])
    	|| empty($options['expectedMaxSize'])
    	)
    	{
    		throw new \Exception('Invalid configuration settings');
    	}
    	
    	$bucket = $app->get( 'aws.bucketname' );
    	$s3 = \Aws\S3\S3Client::factory(array(
    			'key' => $app->get('aws.serverPublicKey'),
    			'secret' => $app->get('aws.serverPrivateKey')
    	));
    	
    	$orig_asset = (new \Dsc\Mongo\Collections\Assets)->setState( 'filter.slug', $slug )->getItem();
    	if( $orig_asset == null ) {
    		return false;
    	}

    	$orig_asset->clear( '_id' );
    	$asset_doc = $orig_asset->cast();
    	$assets_collection->insert(
    			$asset_doc,
    			array( 'w' => 1 )
    	);
    	
    	$key = (string)$asset_doc['_id'];
    	 
    	$url = $this->__sync_site_url.'/asset/'.$slug;
    	$request = $web->request( $url );
    	 
    	$res = $s3->putObject(array(
    			'Bucket' => $bucket,
    			'Key' => $key,
    			'Body' => $request['body'],
    			'ContentType' => $orig_asset->contentType,
    	));
    	
    	$s3->waitUntil('ObjectExists', array(
    			'Bucket' => $bucket,
    			'Key'    => $key
    	));
    	
    	$objectInfoValues = $s3->headObject(array(
    			'Bucket' => $bucket,
    			'Key' => $key
    	))->getAll();
    	
    	$asset_doc['url'] = $s3->getObjectUrl($bucket, $key);
    	
    	$asset_doc['s3'] = array_merge( array(), (array) $orig_asset->s3, array(
    			'bucket' => $bucket,
    			'key' => $key,
    			'uuid' => $key,
    	) )  + $objectInfoValues;
    	
    	$assets_collection->update(
    			array( '_id' => new \MongoId( $asset_doc['_id'] ) ),
    			$asset_doc,
    			array( 'multi' => false )
    	);
    	 
    }
}