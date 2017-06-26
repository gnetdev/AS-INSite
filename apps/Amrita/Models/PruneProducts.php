<?php
namespace Amrita\Models;

class PruneProducts extends \Netsuite\Models\BaseImportExport
{
    protected $__type = 'amrita.prune-products';
    protected $__logCategory = 'PruneProducts';

    public function run()
    {
        if (! $this->canRun())
        {
            return false;
        }
        
        return $this->startRunning();
    }

    protected function canRun()
    {
        if (! $this->isExportComplete())
        {
            return false;
        }
        
        $currentStatus = $this->getCurrentStatus();
        
        // If the previous process is finished and it was last finished yesterday, run it again from the very beginning (reset current status)
        if (! empty( $currentStatus->is_finished ) && (date( 'Y-m-d', $currentStatus->{'finished.time'} ) <= date( 'Y-m-d', strtotime( 'yesterday' ) )))
        {
            $this->log( "Restarting the product prune after today's import and export.", 'INFO', $this->__logCategory );
            $this->resetCurrentStatus();
            
            return true;
        }
        
        // else If the process is finished and it was finished today, don't clutter the logs. don't do anything until tomorrow
        elseif (! empty( $currentStatus->is_finished ) && (date( 'Y-m-d', $currentStatus->{'finished.time'} ) == date( 'Y-m-d', strtotime( 'now' ) )))
        {
            return false;
        }
        
        return true;
    }

    /**
     * Checks if the \Netsuite\Models\Export\ShopProducts is complete
     *
     * @return boolean
     */
    public function isExportComplete()
    {
        $exporter = new \Netsuite\Models\Export\ShopProducts();
        $model = new \Dsc\Mongo\Collections\Settings();
        $model->load( array(
            'type' => $exporter->type(),
            'name' => 'netsuite-models-export-shopproducts' 
        ) );
        $currentStatus = new \Netsuite\Models\Prefabs\CurrentStatus( $model->get( 'current_status' ) );
        if (! empty( $currentStatus->is_finished ) && (date( 'Y-m-d', $currentStatus->{'finished.time'} ) == date( 'Y-m-d', strtotime( 'now' ) )))
        {
            return true;
        }
        
        return false;
    }

    protected function startRunning()
    {
        $this->log( 'Pruning products.', 'INFO', $this->__logCategory );
        
        $this->pruneProducts()->pruneVariants();
        
        $currentStatus = $this->getCurrentStatus();
        
        $currentStatus->set( 'is_finished', true );
        $currentStatus->set( 'finished', \Dsc\Mongo\Metastamp::getDate( 'now' ) );
        $this->setCurrentStatus( $currentStatus->cast() );
        $this->log( 'Pruned products for today.', 'INFO', $this->__logCategory );
        
        return true;
    }

    public function pruneProducts()
    {
        // get all the netsuite ids for parent products that were imported today
        $todays_imported_netsuite_ids = (new \Netsuite\Models\Import\Products())->collection()->distinct( 'ns_id', array(
            'last_imported' => array(
                '$exists' => true 
            ),
            'last_imported.time' => array(
                '$gte' => strtotime( 'today' ) 
            ),
            'parent.value' => array(
                '$in' => [
                    '',
                    null 
                ] 
            ) 
        ) );
        
        // remove all f3-shop products if they have a netsuite.id and the netsuite.id is not in the above array of ids
        $products_to_remove = (new \Shop\Models\Products())->collection()->update( array(
            'netsuite.id' => array(
                '$exists' => true 
            ),
            'netsuite.id' => array(
                '$nin' => $todays_imported_netsuite_ids 
            ) 
        ), array(
        	'$set' => array(
        	   'publication.status' => 'inactive'
            )
        ) );
        
        $this->log( \Dsc\Debug::dump( $products_to_remove ), 'INFO', 'PruneProducts' );
        
        return $this;
    }

    public function pruneVariants()
    {
        // select all items in the imported products list that DO have a shop.variant_id
        // but were NOT imported today
        $variants_not_imported_today = (new \Netsuite\Models\Import\Products())->collection()->distinct( 'shop.variant_id', array(
            '$or' => array(
                array(
                    'last_imported.time' => array(
                        '$lte' => strtotime( 'today' )
                    )
                ),
                array(
                    'last_imported' => array(
                        '$exists' => false
                    )
                )
            ),
            'parent.value' => array(
                '$nin' => [
                '',
                null
                ]
            ),
            'shop.variant_id' => array(
                '$exists' => true
            ),
        ) );
        
        // get all of their products
        $products_to_edit = (new \Shop\Models\Products())->collection()->find( array(
            'variants.id' => array(
                '$in' => $variants_not_imported_today
            )
        ));
        
        // disable the variants
        $message = count( $variants_not_imported_today ) . ' variants from the imported products cache were NOT in today\'s product import.';
        $this->log( $message, 'INFO', 'PruneVariants' );
        
        $message = count( iterator_to_array( $products_to_edit ) ) . ' of those variants exist as children in the products database, and they will be disabled if not already.';
        $this->log( $message, 'INFO', 'PruneVariants' );
        
        $message = null;
        $count = 0;
        foreach ($products_to_edit as $v)
        {
            $product = new \Shop\Models\Products($v);
            array_walk($product->variants, function(&$item, $key) use($variants_not_imported_today, &$message, &$count)
            {
                if (in_array($item['id'], $variants_not_imported_today))
                {
                    if (!empty($item['enabled'])) 
                    {
                        $item['enabled'] = 0;
                        $count++;
                        $message .= 'Disabling ' . \Dsc\ArrayHelper::get($item, 'sku') . ' - Netsuite ID: ' . \Dsc\ArrayHelper::get($item, 'netsuite.id') . '<br/>';
                    }
                }
            });

            try {
                $product->save();
            } 
            catch(\Exception $e) {
                $errorMessage = "Caught Error saving netsuite id: " . $product->{'netsuite.id'} . ". Error: " . $e->getMessage();
                $this->log( $errorMessage, 'ERROR', $this->__logCategory );
            }
            
        }        
        
        $this->log( $count . ' variants disabled.' , 'INFO', 'PruneVariants' );
        
        if (!empty($message)) {
            $this->log( $message, 'INFO', 'PruneVariants' );
        }
        
        return $this;
    }
}