<?php
namespace Amrita\Models\Import;

class RefundFigures extends \Netsuite\Models\BaseImportExport
{
    public $ns_id;
    public $ns_last_modified;
    protected $__collection_name = 'amrita.refund_figures';
    protected $__config = array(
        'default_sort' => array(
            'ns_id' => 1 
        ) 
    );
    protected $__logCategory = 'ImportRefundFigures';
    protected $__pageSize = 500;
    protected $__max_loops = 1;

    protected function canRun()
    {
        $currentStatus = $this->getCurrentStatus();
        
        if (! empty( $currentStatus->search_id ))
        {
            // When was this import last finished?
            // if is finished and it was last finished yesterday, run it again from the very beginning
            if (! empty( $currentStatus->is_finished ) && (date( 'Y-m-d', $currentStatus->{'finished.time'} ) <= date( 'Y-m-d', strtotime( 'yesterday' ) )))
            {
                $this->log( 'Restarting import.', 'INFO', $this->__logCategory );
                $this->resetCurrentStatus();
                
                return true;
            }
            
            // If the import is finished and it was finished today, don't clutter the logs
            elseif (! empty( $currentStatus->is_finished ) && (date( 'Y-m-d', $currentStatus->{'finished.time'} ) == date( 'Y-m-d', strtotime( 'now' ) )))
            {
                return false;
            }
            
            // If the import is finished, mark it as finished
            elseif ($currentStatus->totalResults < ($currentStatus->resultsOffset + $currentStatus->resultsLimit))
            {
                $currentStatus->set( 'is_finished', true );
                $currentStatus->set( 'finished', \Dsc\Mongo\Metastamp::getDate( 'now' ) );
                $this->setCurrentStatus( $currentStatus->cast() );
                $this->log( 'Import is finished for today.', 'INFO', $this->__logCategory );
                
                return false;
            }
        }
        
        return true;
    }

    protected function isRunning()
    {
        $currentStatus = $this->getCurrentStatus();
        
        // TODO Change this from looking at search_id
        // to looking at the is_finished field and the date of last_modified ?
        if (! empty( $currentStatus->search_id ))
        {
            
            $errorMessage = "Currently on item " . ($currentStatus->resultsOffset + $currentStatus->resultsLimit) . " of " . $currentStatus->totalResults;
            $this->log( $errorMessage, 'INFO', $this->__logCategory );
            
            return true;
        }
        
        return false;
    }

    protected function startRunning()
    {
        $searchResponse = $this->fetchRecords();
        
        if (empty( $searchResponse ))
        {
            
            $return = false;
            
            $errorMessage = "Empty response from restlet";
            $this->log( $errorMessage, 'ERROR', $this->__logCategory );
            
            $currentStatus = $this->getCurrentStatusObject();
            $this->setCurrentStatus( $currentStatus );
        }
        else
        {
            
            $return = true;
            
            $currentStatus = clone $searchResponse;
            unset( $currentStatus->results );
            $this->setCurrentStatus( $currentStatus );
            
            $errorMessage = "startImport found " . $searchResponse->totalResults . " records.";
            $this->log( $errorMessage, 'INFO', $this->__logCategory );
            
            try
            {
                $this->saveRecords( $searchResponse->results );
            }
            catch ( \Exception $e )
            {
                $errorMessage = $e->getMessage();
                $this->log( $errorMessage, 'ERROR', $this->__logCategory );
            }
        }
        
        return $return;
    }

    protected function continueRunning()
    {
        for($i = 1; $i <= $this->__max_loops; $i ++)
        {
            set_time_limit( 0 );
            
            $currentStatus = $this->getCurrentStatus();
            $nextOffset = $currentStatus->resultsOffset + $currentStatus->resultsLimit;
            $searchResponse = $this->fetchRecords( $nextOffset );
            
            if (empty( $searchResponse ))
            {
                
                $return = false;
                
                $errorMessage = "Empty response from restlet";
                $this->log( $errorMessage, 'ERROR', $this->__logCategory );
                
                $currentStatus = $this->getCurrentStatusObject();
                $this->setCurrentStatus( $currentStatus->cast() );
            }
            else
            {
                
                $return = true;
                
                try
                {
                    $clone = clone $searchResponse;
                    unset( $clone->results );
                    $currentStatus = $this->getCurrentStatusObject();
                    $currentStatus->bind( $clone );
                    $this->setCurrentStatus( $currentStatus->cast() );
                }
                catch ( \Exception $e )
                {
                    $errorMessage = $e->getMessage();
                    $this->log( $errorMessage, 'ERROR', $this->__logCategory );
                }
                
                $errorMessage = "continueImport found " . $searchResponse->totalResults . " records, but processed only " . $searchResponse->resultsLimit . " items starting from item " . $searchResponse->resultsOffset;
                $this->log( $errorMessage, 'INFO', $this->__logCategory );
                
                try
                {
                    $this->saveRecords( $searchResponse->results );
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
    
    public function doFetch( $offset=null, $limit=null )
    {
        return $this->fetchRecords( $offset, $limit );
    }
    
    public function doFetchAndSave($offset = null, $limit = null)
    {
        $records = $this->doFetch($offset, $limit);
    
        if (!empty($records->results))
        {
            $this->saveRecords( $records->results );
        }
        else
        {
            $errorMessage = "No records to save";
            $this->log($errorMessage, 'INFO', $this->__logCategory);
        }
    
        return $this;
    }    

    protected function fetchRecords( $offset = null, $limit = null )
    {
        // TODO Make this an input
        $url = 'https://rest.netsuite.com/app/site/hosting/restlet.nl?script=117&deploy=1';
        if (! empty( $offset ))
        {
            $url .= '&offset=' . $offset;
        }
        if (! empty( $limit ))
        {
            $url .= '&limit=' . $limit;
        }
        
        // TODO Use inputs from config
        $headers = array(
            'Authorization: NLAuth nlauth_account=838520, nlauth_email=rdiaztushman@dioscouri.com, nlauth_signature=raf2013, nlauth_role=18',
            'Content-Type: application/json' 
        );
        
        $this->_curl = curl_init();
        $opts = array(
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTPGET => true,
            CURLOPT_POST => false,
            CURLOPT_AUTOREFERER => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER => false,
            CURLOPT_NOBODY => false,
            CURLOPT_TIMEOUT => 0 
        );
        
        curl_setopt_array( $this->_curl, $opts );
        curl_setopt( $this->_curl, CURLOPT_URL, $url );
        curl_setopt( $this->_curl, CURLOPT_SSL_VERIFYPEER, false );
        curl_setopt( $this->_curl, CURLINFO_HEADER_OUT, true ); // enable tracking
        curl_setopt( $this->_curl, CURLOPT_HTTPHEADER, $headers );
        
        set_time_limit( 0 );
        
        $start = microtime( true );
        $response_raw = curl_exec( $this->_curl );
        $time_taken = microtime( true ) - $start;
        
        $errorMessage = "fetchRecords took " . number_format( $time_taken, 4 ) . " seconds";
        $this->log( $errorMessage, 'INFO', $this->__logCategory );
        
        // $this->log($response_raw, 'INFO', $this->__logCategory);
        
        $response = json_decode( $response_raw );
        
        return $response;
    }

    protected function saveRecords( array $records )
    {
        $start = microtime( true );
        foreach ( $records as $record )
        {
            set_time_limit( 0 );
            
            $model = new static();
            
            $model->load( array(
                'ns_id' => $record->id,
                'line_id' => $record->line_id 
            ) );
            
            foreach ( $record as $key => $value )
            {
                switch ($key)
                {
                    case "id" :
                        $model->set( 'ns_id', $value );
                        break;
                    default :
                        if (! empty( $key ) && ! is_null( $value ))
                        {
                            $model->set( $key, $value );
                        }
                        break;
                }
            }
            
            $model->set( 'last_imported', \Dsc\Mongo\Metastamp::getDate( 'now' ) );
            $model->processed = false;
            
            $model->save();
        }
        $time_taken = microtime( true ) - $start;
        
        $errorMessage = "saved " . count( $records ) . " records in " . number_format( $time_taken, 4 ) . " seconds";
        $this->log( $errorMessage, 'INFO', $this->__logCategory );
    }

    protected function beforeSave()
    {
        // convert the netsuite last modified value to a Metastamp for easier searching during export
        $this->set( 'ns_last_modified', \Dsc\Mongo\Metastamp::getDate( $this->get( 'Refund Last Modified Date.value' ) ) );
        
        $this->set( 'metadata.last_modified', \Dsc\Mongo\Metastamp::getDate( 'now' ) );
        
        return parent::beforeSave();
    }
    
    protected function afterSave()
    {
        $this->findAndSetSalesOrderNumber();
        
        return parent::afterSave();
    }    

    protected function beforeValidate()
    {
        if (! $this->get( 'metadata.created' ))
        {
            $this->set( 'metadata.created', \Dsc\Mongo\Metastamp::getDate( 'now' ) );
        }
        
        return parent::beforeValidate();
    }

    protected function fetchConditions()
    {
        parent::fetchConditions();
        
        $filter_keyword = $this->getState( 'filter.keyword' );
        if ($filter_keyword && is_string( $filter_keyword ))
        {
            $key = new \MongoRegex( '/' . $filter_keyword . '/i' );
            
            $where = array();
            $where[] = array(
                'Netsuite Sales Order Number.value' => $key 
            );
            $where[] = array(
                'ns_id' => $key 
            );
            
            $this->setCondition( '$or', $where );
        }
        
        $filter_has_shop_product = $this->getState( 'filter.has_shop_product' );
        if (strlen( $filter_has_shop_product ) || is_bool( $filter_has_shop_product ))
        {
            if (empty( $filter_has_shop_product ))
            {
                // only those who do NOT have a product association
                $this->setCondition( 'shop.product_id', array(
                    '$exists' => false 
                ) );
            }
            else
            {
                // only those that DO
                $this->setCondition( 'shop.product_id', array(
                    '$exists' => true,
                    '$nin' => array() 
                ) );
            }
        }
        
        $filter_has_parent = $this->getState( 'filter.has_parent' );
        if (strlen( $filter_has_parent ) || is_bool( $filter_has_parent ))
        {
            if (empty( $filter_has_parent ))
            {
                // only those who do NOT have a parent
                $this->setCondition( 'parent.value', array(
                    '$in' => array(
                        '',
                        '0',
                        null 
                    ) 
                ) );
            }
            else
            {
                // only those that DO
                $this->setCondition( 'shop.product_id', array(
                    '$gt' => '0' 
                ) );
            }
        }
        
        $filter_netsuite_id = $this->getState( 'filter.netsuite_id' );
        if (strlen( $filter_netsuite_id ))
        {
            $this->setCondition( 'ns_id', $filter_netsuite_id );
        }
        
        $filter_parent = $this->getState( 'filter.parent' );
        if (strlen( $filter_parent ))
        {
            $this->setCondition( 'parent.value', $filter_parent );
        }
        
        $filter_do_not_import = $this->getState( 'filter.do_not_import' );
        if (is_bool( $filter_do_not_import ))
        {
            if ($filter_do_not_import)
            {
                $this->setCondition( 'do_not_import', array(
                    '$exists' => true,
                    '$in' => array(
                        true,
                        '1' 
                    ) 
                ) );
            }
            else
            {
                $this->setCondition( 'do_not_import', array(
                    '$nin' => array(
                        true,
                        '1' 
                    ) 
                ) );
            }
        }
        
        $filter_imported_today = $this->getState( 'filter.imported_today' );
        if (strlen( $filter_imported_today ))
        {
            $this->setCondition( 'last_imported.time', array(
                '$gte' => strtotime( 'today' ) 
            ) );
        }
        
        $filter_no_parent_found = $this->getState( 'filter.no_parent_found' );
        if (strlen( $filter_no_parent_found ))
        {
            if ($filter_no_parent_found)
            {
                $this->setCondition( 'no_parent_found', array(
                    '$exists' => true,
                    '$in' => array(
                        true,
                        '1'
                    )
                ) );
            }
            else
            {
                $this->setCondition( 'no_parent_found', array(
                    '$nin' => array(
                        true,
                        '1'
                    )
                ) );
            }
        }

        $filter_recent_error = $this->getState( 'filter.recent_error' );
        if (strlen( $filter_recent_error ))
        {
            if ($filter_recent_error)
            {
                $this->setCondition( 'recent_error', array(
                    '$exists' => true,
                    '$in' => array(
                        true,
                        '1'
                    )
                ) );
            }
            else
            {
                $this->setCondition( 'recent_error', array(
                    '$nin' => array(
                        true,
                        '1'
                    )
                ) );
            }
        }
        
        $filter_processed = $this->getState( 'filter.processed' );
        if (is_bool( $filter_processed ))
        {
            if ($filter_processed)
            {
                $this->setCondition( 'processed', array(
                    '$exists' => true,
                    '$in' => array(
                        true,
                        '1'
                    )
                ) );
            }
            else
            {
                $this->setCondition( 'processed', array(
                    '$nin' => array(
                        true,
                        '1'
                    )
                ) );
            }
        }        
        
        $filter_has_ns_so_id = $this->getState( 'filter.has_ns_so_id' );
        if (strlen( $filter_has_ns_so_id ) || is_bool( $filter_has_ns_so_id ))
        {
            if (empty( $filter_has_ns_so_id ))
            {
                // only those that do NOT
                $this->setCondition( 'Netsuite Sales Order Number.value', array(
                    '$in' => array(
                        '',
                        '0',
                        null
                    )
                ) );
            }
            else
            {
                // only those that DO
                $this->setCondition( 'Netsuite Sales Order Number.value', array(
                    '$nin' => array(
                        '',
                        '0',
                        null
                    )
                ) );
            }
        }        
        
        return $this;
    }
    
    public function shopOrder()
    {
        $this->__shopOrderError = null;
        
        if ($sales_order_id = $this->{'Netsuite Sales Order Number.value'})
        {
            $order = \Shop\Models\Orders::findOne(array(
                'netsuite.id' => $sales_order_id
            ));
        
            if (empty($order->id))
            {
                $this->__shopOrderError = 'No Shop Order found';
                return false;
            }
        }
        
        else
        {
            $this->__shopOrderError = "No sales order ID";
            return false;
        }

        return $order;
    }
    
    public function findAndSetSalesOrderNumber() 
    {
        if (empty($this->{'Netsuite Sales Order Number.value'})) 
        {
            foreach(static::collection()->find(array(
                'ns_id' => $this->ns_id
            )) as $doc) 
            {
                if (!empty($doc['Netsuite Sales Order Number']['value'])) 
                {
                    $this->{'Netsuite Sales Order Number.value'} = $doc['Netsuite Sales Order Number']['value'];
                    $this->store();
                    break;
                }
            }
        }
        
        return $this;
    }
    
    public static function importAndExport()
    {
        $model = new \Amrita\Models\Import\RefundFigures();
        $model->doFetchAndSave();
    
        $model = new \Amrita\Models\Export\RefundFigures();
        $model->doFetchAndSave();
    }
}
