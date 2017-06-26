<?php
namespace Amrita\Site\Listeners;

class Users extends \Prefab 
{
	public function afterUserLogin( $event ) 
	{
	    $user = $event->getArgument('identity');
	    
	    $last_order_sync_datetime = $user->get('netsuite.last_order_sync_datetime');
	    if (empty($last_order_sync_datetime) || strtotime( $last_order_sync_datetime ) < strtotime('today') )
	    {
	        \Dsc\Queue::task('\Amrita\Models\Customers::sync', array('id' => $user->id), array(
	            'title' => 'Sync Netsuite customer and order history for: ' . $user->fullName()
	        ));
	    }	   

	    if (!empty($user->{'last_mailchimp_birthday_sync'}) && !empty($user->birthday))
	    {
    	    \Dsc\Queue::task('\Amrita\Models\Customers::sendBirthdayToMailchimp', array('id' => $user->id), array(
    	        'title' => 'Send birthday to mailchimp for: ' . $user->email
    	    ));
	    }
	}
	
	public function afterCreateUsersModelsUsers( $event ) 
	{
	    $settings = \Amrita\Models\Settings::fetch();
	    $mailchimp_api_key = $settings->{'mailchimp.api_key'};
	    $mailchimp_main_list_id = $settings->{'mailchimp.main_list_id'};
	    if ($mailchimp_api_key && $mailchimp_main_list_id) 
	    {
	        $user = $event->getArgument('model');
	        if (!empty($user->email))
	        {
	            $MailChimp = new \Drewm\MailChimp($mailchimp_api_key);
	            $result = $MailChimp->call('lists/subscribe', array(
	                'id'                => $mailchimp_main_list_id,
	                'email'             => array('email'=>$user->email),
	                'merge_vars'        => array('FNAME'=>$user->first_name, 'LNAME'=>$user->last_name),
	                'double_optin'      => false,
	                'update_existing'   => true,
	                'replace_interests' => false,
	                'send_welcome'      => false,
	            ));
	        
	            if (!empty($result))
	            {
	                if (!empty($result->error))
	                {
	        
	                }
	                else
	                {
	                    // update kissmetrics, if you can
	                    $settings = \Admin\Models\Settings::fetch();
	                    if( class_exists( '\KM' ) && $settings->enabledIntegration('kissmetrics')){
	                        \KM::init( $settings->{'integration.kissmetrics.key'} );
	        
	                        \KM::identify( $user->email );
	                        \KM::record("Subscribed to Newsletter", array(
	                            'Newsletter Name' => "Main",
	                            'E-mail' => $user->email,
	                        ));
	                    }
	        
	                    $user->{'mailchimp.' . $mailchimp_main_list_id} = $result;
	                    $user->save();
	                }
	            }
	        }
	    }	    
	}
}