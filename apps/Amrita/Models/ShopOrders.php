<?php 
namespace Amrita\Models;

class ShopOrders extends \Shop\Models\Orders
{
    protected function fetchConditions()
    {
        parent::fetchConditions();
        
        $filter_has_ns_id = $this->getState('filter.has_ns_id');
        if (strlen($filter_has_ns_id))
        {
            if (empty($filter_has_ns_id)) 
            {
                $this->setCondition('netsuite.id', array('$exists' => false));
            }
            else 
            {
                $this->setCondition('netsuite.id', array('$nin' => array('', null)));
            }            
        }
        
        return $this;
    }
        
    public static function fromSalesOrder( \Netsuite\Models\SalesOrder $salesOrder )
    {
        $order = (new \Shop\Models\Orders)->load(array('netsuite.id' => $salesOrder->internalId));
    
        if (empty($order->id))
        {
            // Create an archived order for it, but only if this user is not a wholesaler
            if ($customer_ns_id = $salesOrder->{'entity.internalId'})
            {
                $user = (new \Users\Models\Users)->load(array('netsuite.id' => $customer_ns_id));
                if (!empty($user->id))
                {
                    $sync = true;
                    foreach ($user->groups as $group) 
                    {
                        switch(@$group['slug']) 
                        {
                            case "no-order-synchronization":
                            case "wholesale-offline-ordering":
                            case "wholesale":
                                $sync = false;
                                break;
                        }
                    }

                    if ($sync)
                    {
                        // OK, this user is not a wholesaler, so lets create the archived order
                        try {
                            $order = \Netsuite\Models\SalesOrder::toShopOrder( $salesOrder, $user );
                            $order->source = array(
                                'id' => 'netsuite-import',
                                'title' => 'Netsuite Import',
                                'description' => 'Imported from Netsuite and originated on the old amritasingh.com'
                            );
                            $order->save();
                        }
                        catch(\Exception $e) {
                             
                        }
                    }
                }
            }
        }
        
        // the order already exists in the website, so update it
        else 
        {
            // Update certain properties, including status, tracking numbers, etc
            $order = $salesOrder->setShopOrderStatuses( $order );
            
            $order->number = $salesOrder->tranId;
            
            if (!empty($salesOrder->linkedTrackingNumbers)) 
            {
                if ($tns = explode(' ', $salesOrder->linkedTrackingNumbers)) 
                {
                    $order->tracking_numbers = array();
                    foreach ($tns as $tn) 
                    {
                        $order->tracking_numbers[] = trim( strtolower($tn) );
                    } 
                }
            }
            
            try {
                $order->save();
            }
            catch(\Exception $e) {
                 
            }            
        }
    }
    
    public static function pushToNetsuite( $id )
    {
        $order = (new static)->setState('filter.id', $id)->getItem();
        
        if (empty($order->id)) 
        {
            throw new \Exception( 'Invalid Order ID' );
        }
    
        // Fuck Netsuite. Handle the "Only one request may be made against a session at a time" error
        $attempt = true;
        while ($attempt)
        {
            set_time_limit(0);
    
            try
            {
                // populate the order, creating customer + addresses if necessary
                $salesOrder = \Amrita\Models\SalesOrder::fromShopOrder($order);
    
                // attempt to push the order to netsuite
                $service = \Netsuite\Factory::instance()->getService();
                $request = new \AddRequest();
                $request->record = $salesOrder;
                $addResponse = $service->add($request);
    
                $attempt = false;
            }
            catch (\Exception $e)
            {
                if (strtolower($e->getMessage()) == "only one request may be made against a session at a time")
                {
                    sleep(5);
                }
    
                else
                {
                    $order->log($e->getMessage(), 'ERROR', __CLASS__ . '::' . __METHOD__);
                    $attempt = false;
                    return;
                }
            }
        }
    
        // if order creation fails in netsuite, say so
        if (!$addResponse->writeResponse->status->isSuccess)
        {
            $order->log($addResponse->writeResponse->status->statusDetail[0]->message, 'ERROR', __CLASS__ . '::' . __METHOD__);
    
            throw new \Exception( 'Unable to push order ID ' . $id . ': ' . $addResponse->writeResponse->status->statusDetail[0]->message );
        }
    
        // else, order creation was successful
        else
        {
            $netsuite_order_id = $addResponse->writeResponse->baseRef->internalId;
            $order->set('netsuite', array(
                'id' => $netsuite_order_id
            ))->store();
        }
    
        return $netsuite_order_id;
    }    
}