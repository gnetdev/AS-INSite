<?php
namespace Amrita\Models\Import;

class Products extends \Netsuite\Models\BaseImportExport
{
    public $ns_id;
    public $ns_last_modified;
    protected $__collection_name = 'netsuite.importproducts.restlet';
    protected $__config = array(
        'default_sort' => array(
            'ns_id' => 1 
        ) 
    );
    protected $__logCategory = 'ImportProducts';
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
            
            // else if the process is == locked (another process has locked it), then let the other process finish before running this
            elseif (!empty($currentStatus->is_locked))
            {
                return false;
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
        // Here, mark is_processing = true in the $currentStatus object and save it
        $currentStatus = $this->getCurrentStatusObject();
        $currentStatus->set( 'is_locked', true );
        $this->setCurrentStatus( $currentStatus->cast() );
                
        $searchResponse = $this->fetchRecords();
        
        if (empty( $searchResponse ))
        {
            
            $return = false;
            
            $errorMessage = "Empty response from restlet";
            $this->log( $errorMessage, 'ERROR', $this->__logCategory );
            
            $currentStatus = $this->getCurrentStatusObject();
            $currentStatus->set( 'is_locked', false );
            $this->setCurrentStatus( $currentStatus );
        }
        else
        {
            $return = true;
            
            $currentStatus = clone $searchResponse;
            unset( $currentStatus->results );
            $currentStatus->is_locked = false;
            
            // this must be done with each restart
            $currentStatus->totalResults = $this->getItemCount();
            
            $this->setCurrentStatus( $currentStatus );
            
            $settings = $this->settings();
            $settings->totalResults = $currentStatus->totalResults; 
            $settings->store();
            
            $errorMessage = "startImport found " . $currentStatus->totalResults . " records.";
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
            $currentStatus->set( 'is_locked', true );
            $this->setCurrentStatus( $currentStatus->cast() );
                        
            $nextOffset = $currentStatus->resultsOffset + $currentStatus->resultsLimit;
            $searchResponse = $this->fetchRecords( $nextOffset );
            
            if (empty( $searchResponse ))
            {
                
                $return = false;
                
                $errorMessage = "Empty response from restlet";
                $this->log( $errorMessage, 'ERROR', $this->__logCategory );
                
                $currentStatus = $this->getCurrentStatusObject();
                $currentStatus->set( 'is_locked', false );
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
                    $currentStatus->is_locked = false;
                    $this->setCurrentStatus( $currentStatus->cast() );
                }
                catch ( \Exception $e )
                {
                    $errorMessage = $e->getMessage();
                    $this->log( $errorMessage, 'ERROR', $this->__logCategory );
                }
                
                $currentStatus = $this->getCurrentStatus();
                
                $errorMessage = "continueImport found " . $currentStatus->totalResults . " records, but processed only " . $searchResponse->resultsLimit . " items starting from item " . $searchResponse->resultsOffset;
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

    protected function fetchRecords( $offset = null, $limit = null )
    {
        // TODO Make this an input
        $url = 'https://rest.netsuite.com/app/site/hosting/restlet.nl?script=97&deploy=1';
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
                'ns_id' => $record->id 
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
            
            $model->save();
        }
        $time_taken = microtime( true ) - $start;
        
        $errorMessage = "saved " . count( $records ) . " records in " . number_format( $time_taken, 4 ) . " seconds";
        $this->log( $errorMessage, 'INFO', $this->__logCategory );
    }

    protected function beforeSave()
    {
        // convert the netsuite last modified value to a Metastamp for easier searching during export
        $this->set( 'ns_last_modified', \Dsc\Mongo\Metastamp::getDate( $this->get( 'last_modified_in_netsuite.value' ) ) );
        
        $this->set( 'metadata.last_modified', \Dsc\Mongo\Metastamp::getDate( 'now' ) );
        
        return parent::beforeSave();
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
                'display_name.value' => $key 
            );
            $where[] = array(
                'name.value' => $key 
            );
            
            $where[] = array(
                'netsuite_id.value' => $key 
            );
            $where[] = array(
                'parent.value' => $key 
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
                $this->setCondition( 'do_not_import', array(
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
        
        return $this;
    }
    
    public function getItemCount()
    {
        set_time_limit( 0 );
    
        $this->service = \Netsuite\Factory::instance()->getService();
        $search = new \ItemSearchAdvanced();
        $search->savedSearchScriptId = "customsearch1227"; //replace with your internal ID
        $request = new \SearchRequest();
        $request->searchRecord = $search;
        $searchResponse = $this->service->search($request);
    
        $count = $searchResponse->searchResult->totalRecords;
        
        return $count;
    }
    
    /**
     * When this model gets its status, it must use the cached product count
     * 
     * @param string $prefix
     * @return \Netsuite\Models\Prefabs\CurrentStatus
     */
    public function getCurrentStatus( $prefix=null )
    {
        $this_model_class = strtolower( get_class($this) );
        $config_name = str_replace( '\\', '-', $this_model_class . $prefix );
    
        $type = $this->type();
        $model = new \Dsc\Mongo\Collections\Settings;
        $model->load(array('type' => $type, 'name' => $config_name ));
    
        $currentStatus = new \Netsuite\Models\Prefabs\CurrentStatus( $model->get('current_status') );
        if (empty($currentStatus)) {
            $currentStatus = $this->getCurrentStatusObject();
        } else {
            $currentStatus->totalResults = $model->totalResults;
        }
    
        return $currentStatus;
    }
    
    /**
     * When this model sets its status, it must use the cached product count
     * 
     * @param unknown $object
     * @param string $prefix
     * @return \Amrita\Models\Import\Products
     */
    public function setCurrentStatus( $object, $prefix=null )
    {
        $currentStatus = $this->getCurrentStatus( $prefix );
    
        foreach ((array) $object as $key => $value) {
            if (!empty($key)) {
                $currentStatus->$key = $value;
            }
        }
    
        $this_model_class = strtolower( get_class($this) );
        $config_name = str_replace( '\\', '-', $this_model_class . $prefix );
    
        $type = $this->type();
        $model = new \Dsc\Mongo\Collections\Settings;
        $model->load(array('type' => $type, 'name' => $config_name ));
        $model->set('name', $config_name);
        $model->set('type', $type);
        if (!$model->totalResults) {
            $model->totalResults = 0;
        }
        $currentStatus->totalResults = $model->totalResults;
        
        $model->set('current_status', $currentStatus->cast());
        $model->save();
    
        return $this;
    }

    /**
     * When this model resets its status, it must also reset the product count
     * 
     * @param string $prefix
     * @return \Amrita\Models\Import\Products
     */
    public function resetCurrentStatus($prefix=null)
    {
        $currentStatus = $this->getCurrentStatusObject();
    
        $this_model_class = strtolower( get_class($this) );
        $config_name = str_replace( '\\', '-', $this_model_class . $prefix );
    
        $type = $this->type();
        $model = new \Dsc\Mongo\Collections\Settings;
        $model->load(array('type' => $type, 'name' => $config_name ));
        $model->set('name', $config_name);
        $model->set('type', $type);
        $model->set('current_status', $currentStatus->cast());
        $model->set('totalResults', 0);
        $model->save();
    
        return $this;
    }
}
