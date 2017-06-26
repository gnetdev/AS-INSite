<?php 
namespace Amrita\Admin\Controllers;

class Customers extends \Admin\Controllers\BaseAuth 
{
    public function sync()
    {
        $id = $this->inputfilter->clean($this->app->get('PARAMS.id'), 'alnum');
        
        $user = (new \Users\Models\Users)->setState('filter.id', $id)->getItem();
        if (!empty($id)) 
        {
            \Dsc\Queue::task('\Amrita\Models\Customers::sync', array('id' => $user->id, 'force'=>true), array(
                'title' => 'Sync Netsuite customer and order history for: ' . $user->fullName()
            ));
            
            \Dsc\System::addMessage('Customer Sync has been forcefully added to the queue and will be finished shortly.', 'success');
        }
        
        else 
        {
            \Dsc\System::addMessage('Invalid user', 'error');
        }

        $redirect = '/admin/shop/customer/read/' . $id;
        if ($custom_redirect = \Dsc\System::instance()->get( 'session' )->get( 'amrita.sync_customer.redirect' ))
        {
            $redirect = $custom_redirect;
        }        
        
        $this->app->reroute($redirect);
    }
    
    public function downloadOrderHistorySince()
    {
        $id = $this->inputfilter->clean($this->app->get('PARAMS.id'), 'alnum');
        
        $user = (new \Users\Models\Users)->setState('filter.id', $id)->getItem();
        if (!empty($id) && !empty($user->{'netsuite.id'}))
        {
            \Dsc\Queue::task('\Amrita\Models\Customers::downloadOrderHistorySince', array('id' => $user->{'netsuite.id'}), array(
                'title' => 'Download order history for: ' . $user->fullName()
            ));
        
            \Dsc\System::addMessage('Customer order history download has been forcefully added to the queue and will be finished shortly.', 'success');
        }
        
        else
        {
            \Dsc\System::addMessage('Invalid user', 'error');
        }
        
        $redirect = '/admin/shop/customer/read/' . $id;
        if ($custom_redirect = \Dsc\System::instance()->get( 'session' )->get( 'amrita.sync_customer.redirect' ))
        {
            $redirect = $custom_redirect;
        }
        
        $this->app->reroute($redirect);        
    }
}