<?php
namespace Amrita\Models;

class Customers extends \Shop\Models\Customers
{
    public static function sendBirthdayToMailchimp( $user_id )
    {
        $user = (new static)->setState('filter.id', $user_id)->getItem();
        if (empty($user->id)) {
            return;
        }
        
        // Has the user already had their birthday added?
        if (!empty($user->{'last_mailchimp_birthday_sync'})) 
        {
            return;
        }
        
        $settings = \Amrita\Models\Settings::fetch();
        $mailchimp_api_key = $settings->{'mailchimp.api_key'};
        $mailchimp_main_list_id = $settings->{'mailchimp.main_list_id'};
        if ($mailchimp_api_key && $mailchimp_main_list_id)
        {
            if (!empty($user->email) && !empty($user->birthday))
            {
                $MailChimp = new \Drewm\MailChimp($mailchimp_api_key);
                
                $result = $MailChimp->call('helper/lists-for-email', array(
                    'email' => array('email'=>$user->email),
                ));
                
                if (!empty($result) && !empty($result->error))
                {
                    // there was an error, so don't do anything
                    $subscribed = false;
                    
                    static::log( $result->error, 'error', __CLASS__ . '::' . __METHOD__ );
                }
                elseif (!empty($result))
                {
                    $subscribed = false;
                    foreach ((array) $result as $subscription)
                    {
                        if (!empty($subscription['id']) && $subscription['id'] == $mailchimp_main_list_id)
                        {
                            $subscribed = true;
                        }
                    }
                }
                
                // Is the user a subscriber?
                if (!empty($subscribed)) 
                {
                    // do we have a valid birthday?
                    if ($birthday = date('m/d', strtotime($user->birthday))) 
                    {
                        $result = $MailChimp->call('lists/update-member', array(
                            'id'                => $mailchimp_main_list_id,
                            'email'             => array('email'=>$user->email),
                            'merge_vars'        => array('MERGE27'=>$birthday),
                        ));
                        
                        if (!empty($result) && !empty($result->error))
                        {
                            // there was an error, so don't do anything
                            static::log( $result->error, 'error', __CLASS__ . '::' . __METHOD__ );
                            
                        }
                        elseif (!empty($result))
                        {
                            // Store that we've already done this in te $user object
                            $user->{'last_mailchimp_birthday_sync'} = time();
                            $user->store();
                             
                            //static::log( $user->email . ' had their birthday sent to mailchimp as ' . $birthday, 'info', __CLASS__ . '::' . __METHOD__ );
                        }
                    }
                    else 
                    {
                        //static::log( $user->email . ' has not provided a birthday', 'error', __CLASS__ . '::' . __METHOD__ );
                    }
                }
                
                else 
                {
                    //static::log( $user->email . ' is not subscribed, so their birthday could not be updated', 'error', __CLASS__ . '::' . __METHOD__ );
                }
            }
            else 
            {
                //static::log( 'Missing email or birthday', 'error', __CLASS__ . '::' . __METHOD__ );
            }
        }
        else
        {
            //static::log( 'Missing MC settings', 'error', __CLASS__ . '::' . __METHOD__ );
        }
    }
    
    public static function setGroups(\Users\Models\Users $user, \Netsuite\Models\Customer $nsCustomer)
    {
        switch ($nsCustomer->{'category.internalId'})
        {
            case "2": // Web Client (Consumer)
                
                break;
            case "19": // Web Client (Reseller)
                $user = $user->addToGroupsBySlugs(array(
                    'wholesale-reseller',
                    'wholesale'
                ));
                break;
            case "3": // Boutique
            case "4": // Dept Store
            case "5": // Discounter
            case "6": // Chain Store
            case "7": // Online Partner/Website
            case "8": // Private Label
            case "9": // Charity
            case "10": // Online Private Sales
            case "11": // Catelogue/Mail Order
            case "12": // Direct Response            
            case "14": // Vendor
            case "15": // Internal
            case "16": // Intl Distributor
            case "17": // PR/Magazine
            case "18": // Marketing Partner
                $user = $user->addToGroupsBySlugs(array(
                    'wholesale-offline-ordering',
                    'no-order-synchronization',
                ));                
                break;
        }
        
        return $user;
    }
    
    /**
     * Trigger this on login
     * and/or once a day
     *
     * TODO Push this to a model method
     * and add it to the queue on login
     */
    public static function sync($id, $force=false)
    {
        $user = (new static)->setState('filter.id', $id)->getItem();
    
        if (empty($user->id) || empty($user->email))
        {
            return null;
        }
    
        $success = false;
    
        $customer_id = $user->{'netsuite.id'};
    
        // the customer record doesn't exist, so set it
        if (empty($customer_id))
        {
            // if there is an email, try searching the local cache first, then do a soap request if not found locally
            if (!empty($user->email))
            {
                $local_customer = (new \Netsuite\Models\Customer)->load(array('email' => $user->email));
                if (!empty($local_customer->id) && !empty($local_customer->ns_id))
                {
                    $customer_id = $local_customer->ns_id;
                    $user->set('netsuite.id', $local_customer->ns_id);
                    $user->save();
    
                    $success = true;
    
                    // OK -- we have a local cached item -- update it if it is older than 24 hours
                    if ($force || empty($local_customer->id) || empty($local_customer->{'metadata.last_modified.time'}) || $local_customer->{'metadata.last_modified.time'} < strtotime('now -24 hours') )
                    {
                        try {
                            \Netsuite\Models\Customer::fetchById($customer_id);
                        }
                        catch (\Exception $e) {

                        }                        
                    }
                }
                else
                {
                    $customerByEmail = \Netsuite\Models\Customer::fetchByEmail($user->email);
                    if (!empty($customerByEmail->internalId))
                    {
                        $customer_id = $customerByEmail->internalId;
                        $user->set('netsuite.id', $customer_id);
                        $user->save();
    
                        $success = true;
                    }
                }
            }
    
            if (empty($customer_id))
            {
                try {
                    $customer_id = \Netsuite\Models\Customer::createFromUser($user);
    
                    // Get the customer object and save it in the local database
                    \Netsuite\Models\Customer::fetchById($customer_id);
    
                    $user->set('netsuite.id', $customer_id);
                    $user->save();
    
                    $success = true;
    
                }
                catch (\Exception $e)
                {
                    $success = false;
                }
            }
        }
    
        // there IS a customer id, so let's make a request to update it, but only if this hasn't been done in the last 24 hours
        else {
    
            $local_customer = (new \Netsuite\Models\Customer)->load(array('ns_id' => $customer_id));
            if ($force || empty($local_customer->id) || empty($local_customer->{'metadata.last_modified.time'}) || $local_customer->{'metadata.last_modified.time'} < strtotime('now -24 hours') )
            {
                try {
                    \Netsuite\Models\Customer::fetchById($customer_id);
                }
                catch (\Exception $e) {
                    
                }                
    
                try {
                    $user->set('netsuite.id', $customer_id);
                    $user->save();
                }
                catch (\Exception $e) {
                    
                }                
            }
    
            $success = true;
        }
    
        if ($success)
        {
            // export addresses
            $local_customer = (new \Netsuite\Models\Customer)->load(array('ns_id' => $customer_id));
            $local_customer->exportShopAddresses();
    
            try
            {
                $user = \Amrita\Models\Customers::setGroups( $user, $local_customer );
                $user->save();
            }
    
            catch (\Exception $e)
            {
                $user->log($e->getMessage(), 'ERROR', 'AmritaSiteCustomer::sync.setGroups');
            }
    
            // This could take a long time, so consider separating it from this request
            // get any of the customer's orders updated since the last order-sync
            $last_order_sync_datetime = $user->get('netsuite.last_order_sync_datetime');
            if ($force || empty($last_order_sync_datetime) || strtotime( $last_order_sync_datetime ) < strtotime('today') )
            {
                try
                {
                    if ($recently_updated_salesorders = \Netsuite\Models\SalesOrder::fetchByCustomer( $customer_id, $last_order_sync_datetime ))
                    {
                        foreach ($recently_updated_salesorders as $so)
                        {
                            \Amrita\Models\ShopOrders::fromSalesOrder( $so );
                        }
                    }
    
                    $user->set('netsuite.last_order_sync_datetime', date('Y-m-d H:i:s', strtotime('now')) );
                    $user->save();
                }
    
                catch (\Exception $e)
                {
                    $user->log($e->getMessage(), 'ERROR', 'AmritaSiteCustomer::sync.orders');
                }
            }
            
            // if the customer's first or last name is invalid, update it in netsuite
            if (
                empty($local_customer->firstName) || is_null($local_customer->firstName)
                || empty($local_customer->lastName) || is_null($local_customer->lastName)
            ) {
            
                /**
                 * update the customer in NS
                 */
                $service = \Netsuite\Factory::instance()->getService();
            
                $customer = new \Customer();
                $customer->internalId = $customer_id;
                $customer->firstName = $user->first_name;
                $customer->lastName = $user->last_name;
            
                $request = new \UpdateRequest();
                $request->record = $customer;
                 
                $response = $service->update($request);
            
                if (empty($response->writeResponse->status->isSuccess))
                {
                    $user->log( $response->writeResponse->status->statusDetail[0]->message );
                }
            }
            
        }
        
        return $user;
    }

    public static function downloadOrderHistorySince($customer_id, $date=null)
    {
        try
        {
            if ($recently_updated_salesorders = \Netsuite\Models\SalesOrder::fetchByCustomer( $customer_id, $date ))
            {
                foreach ($recently_updated_salesorders as $so)
                {
                    \Amrita\Models\ShopOrders::fromSalesOrder( $so );
                }
            }
        }
        
        catch (\Exception $e)
        {
            (new static)->log($e->getMessage(), 'ERROR', 'AmritaSiteCustomer::downloadOrderHistorySince');
        }    
    }

    public static function loyaltyLevel($id)
    {
        if (is_object($id) && is_a($id, '\Users\Models\Users')) 
        {
            $user = $id;
        } 
        else 
        {
            $user = (new static)->setState('filter.id', $id)->getItem();
        }
    
        if (empty($user->id))
        {
            return null;
        }
        
        if (empty($user->groups)) 
        {
            return null;
        }
        
        foreach ((array) $user->groups as $group) 
        {
            if (!empty($group['slug'])) 
            {
                if (strpos($group['slug'], 'banglebabe-') === 0) 
                {
                    $group['campaign'] = array();
                    if (!empty($user->{'shop.active_campaigns'})) 
                    {
                        foreach ((array) $user->{'shop.active_campaigns'} as $campaign) 
                        {
                            $c = (new \Shop\Models\Campaigns)->setState('filter.id', $campaign['id'])->getItem();
                            if (!empty($c->id) && (string) $c->id == (string) $campaign['id'] && $c->slug == $group['slug']) 
                            {
                                $group['campaign'] = $campaign;
                            }
                        }
                    }
                    return $group;
                }
            }
        }
        
        return null;
    }    
}