<?php 
namespace Amrita\Site\Controllers;

class Customer extends \Dsc\Controller 
{
    /**
     * 
     */
    public function sync($force=false)
    {
        $user = $this->getIdentity();
        
        if (empty($user->id) || empty($user->email)) 
        {
        	return;
        }

        \Dsc\Queue::task('\Amrita\Models\Customers::sync', array('id' => $user->id, 'force' => $force ), array(
            'title' => 'Sync Netsuite customer and order history for: ' . $user->fullName()
        ));
    }
    
    /**
     * Forces a sync immediately
     * 
     */
    public function forceSync()
    {
        $user = $this->getIdentity();
        
        if (empty($user->id) || empty($user->email)) 
        {
        	return;
        }

        $this->sync(true);
    }
}