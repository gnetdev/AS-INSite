<?php 
namespace Amrita\Site\Controllers;

class Support extends \Dsc\Controller 
{
    /**
     * Displays the contact us form
     */
    public function create()
    {
        $this->app->set('meta.title', 'Contact Us');
        
        echo $this->theme->render( 'Amrita/Site/Views::support/create.php' );
    }
    
    /**
     * Target for the contact us form
     */
    public function createSubmit()
    {
        $request = $this->app->get('REQUEST');
        
        $user = \Dsc\System::instance()->get('auth')->getIdentity();
        
        $data = array(
        	'name' => $this->input->get('name', null, 'string'),
            'email' => $this->input->get('email', null, 'string'),
            'phone_number' => $this->input->get('phone_number', null, 'string'),
            'subject' => $this->input->get('subject', null, 'string'),
            'message' => $this->input->get('message', null, 'string'),
            'user_netsuite_id' => $user->{'netsuite.id'},
        );
        
        try {
            \Dsc\Queue::task('\Netsuite\Models\SupportCase::export', array(
                'data' => $data
            ));
        }
        catch (\Exception $e) {
            \Dsc\System::addMessage($e->getMessage(), 'error');
            $this->app->reroute( '/support/case' );
        }

        try {
            $new_email = $data['email'];
            
            //$html = $this->theme->renderView( 'Amrita/Site/Views::support/create-email.php' );
            //$text = $html;
            //$subject = 'Thanks for your message!';
            // $this->__sendEmailToCustomer = $this->mailer->send($new_email, $subject, array($html, $text) );
        }
        catch (\Exception $e) {
            
        }
        
        $this->app->reroute( '/support/case/created' );
    }
    
    public function createConfirmation()
    {
        $this->app->set('meta.title', 'Thank you for contacting Us');
        
        echo $this->theme->render( 'Amrita/Site/Views::support/create-confirmation.php' );        
    }
}