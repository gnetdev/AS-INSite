<?php 
namespace Amrita\Site\Controllers;

class CreditCards extends \Dsc\Controller 
{
    public function beforeRoute()
    {
        $this->requireIdentity();
    }
        
    public function index()
    {
        $this->app->set('meta.title', 'Credit Cards | My Account');
        
        $creditCards = \Netsuite\Models\Customer::creditCardsForUser( $this->getIdentity() );
        $this->app->set('creditCards', $creditCards);
        
        echo $this->theme->render( 'Amrita/Site/Views::creditcards/index.php' );
    }
    
    public function remove()
    {
        $service = \Netsuite\Factory::instance()->getService();
        
        $id = $this->inputfilter->clean( $this->app->get('PARAMS.id'), 'int' );
        $found = false;
        $user = $this->getIdentity();
        
        \Amrita\Models\Customers::sync($user->id, true);
        
        $cards = array();        
        // Validate that $id belongs to this user in the local internalId cache
        if ($creditCards = \Netsuite\Models\Customer::creditCardsForUser( $user )) 
        {
            foreach ($creditCards as $key=>$creditCard) 
            {
                if ($creditCard['internalId'] == $id) 
                {
                    $found = true;
                    unset($creditCards[$key]);
                }
                else 
                {
                    $card = new \CustomerCreditCards;
                    $card->internalId = $creditCard['internalId'];
                    $cards[] = $card;                    
                }
            }
        }        
        
        // if so, make a delete request
        if ($found) 
        {

            $customer_id = $user->{'netsuite.id'};
            
            $customer = new \Customer();
            $customer->internalId = $customer_id;
            $customer->creditCardsList = new \CustomerCreditCardsList();
            $customer->creditCardsList->replaceAll = true;
            $customer->creditCardsList->creditCards = $cards;
            
            $request = new \UpdateRequest();
            $request->record = $customer;
            
            try {
                
                $response = $service->update($request);
                
                if (empty($response->writeResponse->status->isSuccess))
                {
                    throw new \Exception($response->writeResponse->status->statusDetail[0]->message);
                }

                $cust_id = $response->writeResponse->baseRef->internalId;
                
                \Dsc\System::instance()->addMessage( 'Card deleted', 'success');
                
                \Amrita\Models\Customers::sync($user->id, true);
                
            }
            catch(\Exception $e) {
                \Dsc\System::instance()->addMessage( $e->getMessage(), 'error');
            }
        }
        
        else 
        {
            \Dsc\System::instance()->addMessage( 'Card not found', 'error');
        }

        $this->app->reroute( '/user/credit-cards' );
    }
}