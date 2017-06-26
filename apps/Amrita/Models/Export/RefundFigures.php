<?php
namespace Amrita\Models\Export;

class RefundFigures extends \Netsuite\Models\BaseImportExport
{

    public $ns_id;

    public $ns_last_modified;

    protected $__collection_name = 'amrita.refund_figures';

    protected $__config = array(
        'default_sort' => array(
            'ns_id' => -1
        )
    );

    protected $__logCategory = 'ExportRefundFigures';

    protected $__pageSize = 500;

    protected $__max_loops = 1;

    protected function canRun()
    {
        $currentStatus = $this->getCurrentStatus();
        
        // If the previous process is finished and it was last finished yesterday, run it again from the very beginning (reset current status)
        if (!empty($currentStatus->is_finished) && (date('Y-m-d', $currentStatus->{'finished.time'}) <= date('Y-m-d', strtotime('yesterday'))))
        {
            $this->log('Restarting export.', 'INFO', $this->__logCategory);
            $this->resetCurrentStatus();
            
            return true;
        }
        
        // else if the process is == processing (another process is running this), then let the other process finish before running this
        elseif (!empty($currentStatus->is_processing))
        {
            return false;
        }
        
        // else If the process is finished and it was finished today, don't clutter the logs. don't do anything until tomorrow
        elseif (!empty($currentStatus->is_finished) && (date('Y-m-d', $currentStatus->{'finished.time'}) == date('Y-m-d', strtotime('now'))))
        {
            return false;
        }
        
        // else If the import is finished, mark it as finished
        elseif (!empty($currentStatus->total_items) && (int) ($currentStatus->current_page) == (int) $currentStatus->total_pages)
        {
            $currentStatus->set('is_finished', true);
            $currentStatus->set('finished', \Dsc\Mongo\Metastamp::getDate('now'));
            $this->setCurrentStatus($currentStatus->cast());
            $this->log('Export is finished for today.', 'INFO', $this->__logCategory);
            
            return false;
        }
        
        return true;
    }

    protected function isRunning()
    {
        $currentStatus = $this->getCurrentStatus();
        
        if (!empty($currentStatus->total_items) && empty($currentStatus->is_finished))
        {
            return true;
        }
        
        return false;
    }

    protected function startRunning()
    {
        // Here, mark is_processing = true in the $currentStatus object and save it
        $currentStatus = $this->getCurrentStatusObject();
        $currentStatus->set('is_processing', true);
        $this->setCurrentStatus($currentStatus->cast());
        
        $searchResponse = $this->fetchRecords();
        
        if (empty($searchResponse->items))
        {
            
            $errorMessage = "No Search Results";
            $this->log($errorMessage, 'INFO', $this->__logCategory);
            
            $return = false;
            $currentStatus = $this->getCurrentStatusObject();
            $currentStatus->set('is_processing', false);
            $currentStatus->set('is_finished', true);
            $currentStatus->set('finished', \Dsc\Mongo\Metastamp::getDate('now'));
            $this->setCurrentStatus($currentStatus->cast());
            
            \Dsc\System::instance()->addMessage('No search results');
        }
        else
        {
            $return = true;
            
            $currentStatus = $this->getCurrentStatusObject();
            
            $errorMessage = "startRunning found " . $searchResponse->total_items . " records, but is only processing " . $searchResponse->items_per_page . " starting with " . (int) ($currentStatus->current_page) * $searchResponse->items_per_page;
            $this->log($errorMessage, 'INFO', $this->__logCategory);
            
            $clone = clone $searchResponse;
            unset($clone->items);
            $currentStatus->bind($clone);
            
            try
            {
                $this->saveRecords($searchResponse->items);
                $currentStatus->set('is_processing', false);
                $this->setCurrentStatus($currentStatus->cast());
            }
            catch (\Exception $e)
            {
                $errorMessage = $e->getMessage();
                $this->log($errorMessage, 'ERROR', $this->__logCategory);
            }
        }
        
        return $return;
    }

    protected function continueRunning()
    {
        $currentStatus = $this->getCurrentStatus();
        
        if (!isset($currentStatus->current_page))
        {
            return $this->startRunning();
        }
        
        // Here, mark is_processing = true in the $currentStatus object and save it
        $currentStatus->set('is_processing', true);
        $this->setCurrentStatus($currentStatus->cast());
        
        $searchResponse = $this->fetchRecords($currentStatus->current_page, null);
        
        if (empty($searchResponse->items))
        {
            $errorMessage = "No Search Results";
            $this->log($errorMessage, 'INFO', $this->__logCategory);
            
            $return = false;
            $currentStatus = $this->getCurrentStatusObject();
            $currentStatus->set('is_processing', false);
            $currentStatus->set('is_finished', true);
            $currentStatus->set('finished', \Dsc\Mongo\Metastamp::getDate('now'));
            $this->setCurrentStatus($currentStatus->cast());
            
            \Dsc\System::instance()->addMessage('No search results');
        }
        else
        {
            $return = true;
            
            $errorMessage = "continueRunning found " . $searchResponse->total_items . " records, but is only processing " . $searchResponse->items_per_page . " starting with " . (int) ($currentStatus->current_page) * $searchResponse->items_per_page;
            $this->log($errorMessage, 'INFO', $this->__logCategory);
            
            $clone = clone $searchResponse;
            unset($clone->items);
            $currentStatus = $this->getCurrentStatusObject();
            $currentStatus->bind($clone);
            
            try
            {
                $this->saveRecords($searchResponse->items);
                $currentStatus->set('is_processing', false);
                $this->setCurrentStatus($currentStatus->cast());
            }
            catch (\Exception $e)
            {
                $errorMessage = $e->getMessage();
                $this->log($errorMessage, 'ERROR', $this->__logCategory);
            }
        }
        
        return $return;
    }

    public function doFetch($offset = null, $limit = null)
    {
        return $this->fetchRecords($offset, $limit);
    }
    
    public function doFetchAndSave($offset = null, $limit = null)
    {
        $records = $this->doFetch($offset, $limit);
        
        if (!empty($records->items)) 
        {
            $this->saveRecords( $records->items );
        }
        else 
        {
            $errorMessage = "No records to save";
            $this->log($errorMessage, 'INFO', $this->__logCategory);            
        }
        
        return $this;
    }    

    protected function fetchRecords($offset = null, $limit = null)
    {
        $currentStatus = $this->getCurrentStatus();
        
        $model = new \Amrita\Models\Import\RefundFigures();
        
        if (empty($limit))
        {
            $limit = $this->__pageSize;
        }
        
        $model->setState('list.limit', $limit);
        $model->setState('filter.processed', false);
        
        if (!empty($offset))
        {
            $model->setState('list.offset', (int) $offset);
        }
        
        $searchResponse = $model->paginate();
        
        if (!empty($searchResponse))
        {
            if (!empty($offset))
            {
                $searchResponse->setCurrent($offset + 1);
            }
        }
        
        return $searchResponse;
    }

    protected function saveRecords(array $records)
    {
        $count = 0;
        $start = microtime(true);
        foreach ($records as $record)
        {
            set_time_limit(0);
            $this->clearErrors();
            
            try
            {
                if ($result = $this->exportItem($record))
                {
                    $count++;
                }
                else
                {
                    $messages = implode(". ", $this->getErrors());
                    $errorMessage = "Error saving netsuite id: " . $record->{'ns_id'} . ". Error: " . $messages;
                    $this->log($errorMessage, 'ERROR', $this->__logCategory);
                    
                    $message = null;
                    foreach ($this->getErrors() as $m)
                    {
                        $message .= $m->getMessage() . ". ";
                    }
                    
                    $record->set('recent_error', true);
                    $record->set('recent_error_message', $message);
                    $record->set('recent_error_datetime', \Dsc\Mongo\Metastamp::getDate('now'));
                    $record->store();
                }
            }
            catch (\Exception $e)
            {
                $errorMessage = "Caught Error while saving netsuite id: " . $record->{'ns_id'} . ". Error: " . $e->getMessage();
                $this->log($errorMessage, 'ERROR', $this->__logCategory);
                
                $record->set('recent_error', true);
                $record->set('recent_error_message', $e->getMessage());
                $record->set('recent_error_datetime', \Dsc\Mongo\Metastamp::getDate('now'));
                $record->store();
            }
        }
        
        $time_taken = microtime(true) - $start;
        $errorMessage = "saved " . $count . " records in " . number_format($time_taken, 4) . " seconds";
        $this->log($errorMessage, 'INFO', $this->__logCategory);
    }

    /**
     * Takes a row from the mongodb.collection
     * and exports it based on its parent status
     *
     * @param unknown $mapper            
     */
    public function exportItem(&$record)
    {
        $message = null; // TODO Use this for logging when in model
        
        if (!empty($record->{'processed'}))
        {
            throw new \Exception('Record has already been processed');
        }
        
        if ($record->{'Status.value'} != 'refunded' && $record->{'Status.value'} != 'closed')
        {
            $record->processed = true;
            $record->processed_time = \Dsc\Mongo\Metastamp::getDate('now');            
            $record->processing_result = \Dsc\Debug::dump( 'Record status is not "refunded" or "closed"' );
            $record->store();            
            
            throw new \Exception('Record status is not "refunded" or "closed" ');
        }
        
        if (!empty($record->{'Netsuite Sales Order Number.value'}))
        {
            $sales_order_id = $record->{'Netsuite Sales Order Number.value'};
        }
        
        else
        {
            // $cash_sale = \Netsuite\Models\CashSale::fetchById($record->{'associated CC/Cash Sale.value'});
            // $sales_order_id = $cash_sale->{'createdFrom.internalId'};
            
            // $sales_order_id = $cash_sale->{createdFrom.internalId} == the sales order's internal ID
            // $message .= \Dsc\Debug::dump($cash_sale);
            
            $message .= \Dsc\Debug::dump('No sales order ID');
        }
        
        if (!empty($sales_order_id))
        {
            // is there a tienda order for this sales order?
            $order = \Shop\Models\Orders::findOne(array(
                'netsuite.id' => $sales_order_id
            ));
            
            if (empty($order->id))
            {
                $message .= \Dsc\Debug::dump('No Shop Order found');
            }
            
            else
            {
                // did the order originate in the online shop?
                if ($order->{'source.id'} != 'shop')
                {
                    $message .= \Dsc\Debug::dump('Order did not originate in the website');
                }
                else
                {
                    // has this adjustment already been done?
                    // use the ns_id (the RA's internal ID)
                    // and the line_id (line_id.value)
                    // as a two-column index
                    
                    $ns_id = $record->ns_id;
                    $line_id = $record->{'line_id.value'};
                    
                    $adjustment = $order->{'amrita.adjustments.' . $ns_id . '.' . $line_id};
                    if ($adjustment)
                    {
                        // TODO update the adjustment?
                        $message .= \Dsc\Debug::dump('Adjustment has already been made');
                    }
                    else
                    {
                        $amount = 0 + $record->{'associated Refund amount.value'};
                        $date = $record->{'Refund Last Modified Date.value'};
                        $display_title = 'Adjustment';
                        
                        // do the adjustment
                        $adjustment = array(
                            'ns_id' => $ns_id,
                            'line_id' => $line_id,
                            'display_title' => $display_title,
                            'amount' => $amount,
                            'date' => $date
                        );
                        
                        $order->{'amrita.adjustments.' . $ns_id . '.' . $line_id} = $adjustment;
                        
                        $order->grand_total = $order->grand_total + $amount;
                        $order->adjustments_total = $order->adjustments_total + $amount;
                        
                        // add it to the history
                        $order->history[] = array(
                            'created' => \Dsc\Mongo\Metastamp::getDate('now'),
                            'verb' => 'adjusted',
                            'details' => $adjustment
                        );
                        
                        // save it
                        $order->store();
                    }
                }
            }
        }
        
        $record->processed = true;
        $record->processed_time = \Dsc\Mongo\Metastamp::getDate('now');
        $record->processing_result = $message;
        $record->store();
        
        return $record;
    }
}
