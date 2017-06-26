<?php
namespace Shop\Site\Controllers;

class Checkout extends \Dsc\Controller
{
    public function index()
    {
        $cart = \Shop\Models\Carts::fetch();
        // Update product fields stored in cart
        foreach ($cart->validateProducts() as $change) {
        	\Dsc\System::addMessage($change);
        }
        $cart->applyCredit();
        
        if ($cart->quantity() <= 0) 
        {
        	$this->app->reroute('/shop/cart');
        }
        
        \Base::instance()->set( 'cart', $cart );
        
        $identity = $this->getIdentity();
        if (empty( $identity->id ))
        {
            $flash = \Dsc\Flash::instance();
            \Base::instance()->set('flash', $flash );
            
            $this->app->set('meta.title', 'Login or Register | Checkout');
            
            \Shop\Models\Activities::track('Checkout Registration Page');
                        
            $view = \Dsc\System::instance()->get( 'theme' );
            echo $view->render( 'Shop/Site/Views::checkout/identity.php' );
            return;
        }
        $identity->reload();
        
        $this->app->set('meta.title', 'Shipping | Checkout');
        
        $props = array();
        if ($identity->guest) {
            $props['guest'] = true;
        }
        \Shop\Models\Activities::track('Started Checkout', $props);
                
        $view = \Dsc\System::instance()->get( 'theme' );
        echo $view->render( 'Shop/Site/Views::checkout/index.php' );
    }

    /**
     * Displays step 2 (of 2) of the default checkout process
     */
    public function payment()
    {
        $cart = \Shop\Models\Carts::fetch();
        // Update product fields stored in cart
        foreach ($cart->validateProducts() as $change) {
            \Dsc\System::addMessage($change);
        }
        $cart->applyCredit();
                
        \Base::instance()->set( 'cart', $cart );
        
        $identity = $this->getIdentity();
        if (empty( $identity->id ))
        {
            $flash = \Dsc\Flash::instance();
            \Base::instance()->set('flash', $flash );
            
            $this->app->set('meta.title', 'Login or Register | Checkout');
                        
            $view = \Dsc\System::instance()->get( 'theme' );
            echo $view->render( 'Shop/Site/Views::checkout/identity.php' );
            return;
        }
        $identity->reload();
        
        $props = array();
        if ($identity->guest) {
            $props['guest'] = true;
        }        
        \Shop\Models\Activities::track('Reached Payment Step in Checkout', $props);
        
        $this->app->set('meta.title', 'Payment | Checkout');
        
        $view = \Dsc\System::instance()->get( 'theme' );
        echo $view->render( 'Shop/Site/Views::checkout/payment.php' );
    }
    
    /**
     * Displays an order confirmation page
     */
    public function confirmation()
    {
        $just_completed_order_id = \Dsc\System::instance()->get('session')->get('shop.just_completed_order_id' );
        
        if (!empty($just_completed_order_id)) 
        {
            try {
                $order = (new \Shop\Models\Orders)->load(array('_id' => new \MongoId( (string) $just_completed_order_id ) ));
                if (empty($order->id)) 
                {
                	throw new \Exception;
                }
                
                /**
                 * Start Activity Tracking
                 */
                $properties = array(
                    'order_id' => (string) $order->id,
                    'order_number' => (string) $order->number,
                    'Grand Total' => (string) $order->grand_total,
                    'Credit Total' => (string) $order->credit_total,
                    'Products' => array(),
                    'Coupons' => \Joomla\Utilities\ArrayHelper::getColumn( (array) $order->coupons, 'code' ),
                    'Auto Coupons' => \Joomla\Utilities\ArrayHelper::getColumn( (array) $order->auto_coupons, 'code' ),
                );
                
                $identity = $this->getIdentity();
                if ($identity->guest) {
                    $properties['guest'] = true;
                }
                
                foreach ( $order->items as $item )
                {
                    $product = array();
                    $product['Product Name'] = \Dsc\ArrayHelper::get($item, 'product.title');
                    if ( \Dsc\ArrayHelper::get($item, 'attribute_title') ) 
                    {
                        $product['Variant'] =  \Dsc\ArrayHelper::get($item, 'attribute_title');
                    }
                    $properties['Products'][] = $product;
                }                
                
                \Shop\Models\Activities::track('Completed Checkout', $properties);
                $abandoned_cart = \Dsc\System::instance()->get('session')->get( 'shop.notification_email' );
                if( $abandoned_cart == 1 ){ // checkedout abandoned cart
                	\Shop\Models\Activities::track('Completed Checkout Abandoned Email' );
                	\Dsc\System::instance()->get('session')->set( 'shop.notification_email', 0 );
                }
                
                
                /**
                 * END Activity Tracking
                 */
                
                // check coupons and discard used generated codes
                if( count( $order->coupons ) ){
                	foreach( $order->coupons as $coupon ){
                		if( !empty( $coupon['generated_code'] ) ){
                			\Shop\Models\Coupons::collection()->update( 
                							array( 
                								'_id' => new \MongoId( (string)$coupon['_id'] ),
                								'codes.list.code' => (string)$coupon['generated_code']
                								),
                							array( '$set' => array( 'codes.list.$.used' => 1 ) ) );
                		}
                	}
                }
                
                if( count( $order->auto_coupons ) ){
                	foreach( $order->auto_coupons as $coupon ){
                		if( !empty( $coupon['generated_code'] ) ){
                			\Shop\Models\Coupons::collection()->update( 
                							array( 
                								'_id' => new \MongoId( (string)$coupon['_id'] ),
                								'codes.list.code' => (string)$coupon['generated_code']
                								),
                							array( '$set' => array( 'codes.list.$.used' => 1 ) ) );
                		}
                	}
                }
                
            } catch (\Exception $e) {
            	// TODO Handle when it's an invalid order
            }
            
            if (!empty($order->id)) 
            {
                \Base::instance()->set('order', $order);
            }
        }
        
        $this->app->set('meta.title', 'Order Confirmation | Checkout');
        
        $view = \Dsc\System::instance()->get( 'theme' );
        echo $view->render( 'Shop/Site/Views::confirmation/index.php' );
        
        \Dsc\System::instance()->get('session')->set('shop.just_completed_order', false );
        \Dsc\System::instance()->get('session')->set('shop.just_completed_order_id', null );
    }

    /**
     * Adds POST data to the user's cart.
     *
     * Typically the target of checkout forms, allowing custom workflows.
     * Responds according to request method.
     * Validates only the provided data, not the cart.
     */
    public function update()
    {
        // TODO If the select data doesn't validate, return an error message while redirecting back to referring page (if http request)
        // or outputting json_encoded response with array of errrors
        $custom_redirect = \Dsc\System::instance()->get( 'session' )->get( 'site.shop.checkout.redirect' );
        $redirect = $custom_redirect ? $custom_redirect : '/shop/checkout/payment';

        try {
            $cart = \Shop\Models\Carts::fetch();
            
            // Do the selective update, saving the data to the Cart if it validates
            $checkout = $this->input->get( 'checkout', array(), 'array' );
            $cart_checkout = array_merge( (array) $cart->{'checkout'}, $checkout );
            $cart->set('checkout', $cart_checkout);
            
            $cart->save();
            
            if ($this->app->get( 'AJAX' ))
            {
                return $this->outputJson( $this->getJsonResponse( array(
                    'result'=>true
                ) ) );                
            }
            else
            {
                $this->session->set( 'site.shop.checkout.redirect', null );
                $this->app->reroute( $redirect );
            }
            
        }
        catch (\Exception $e) {
        	
            if ($this->app->get( 'AJAX' ))
            {
                return $this->outputJson( $this->getJsonResponse( array(
                    'result'=>false,
                    'message'=>$e->getMessage()
                ) ) );                
            }
            else
            {
                \Dsc\System::addMessage($e->getMessage(), 'error');
                $this->session->set( 'site.shop.checkout.redirect', null );
                $this->app->reroute( $redirect );
            }
                        
        }
        
        return;
    }

    /**
     * Gets valid shipping methods for the cart
     */
    public function shippingMethods()
    {
        $cart = \Shop\Models\Carts::fetch();
        \Base::instance()->set( 'cart', $cart );
        
        $view = \Dsc\System::instance()->get('theme');
        echo $view->renderView('Shop/Site/Views::checkout/shipping_methods.php');
    }
    
    /**
     * Gets valid payment methods for the cart
     */
    public function paymentMethods()
    {
        $cart = \Shop\Models\Carts::fetch();
        \Base::instance()->set( 'cart', $cart );
    
        $view = \Dsc\System::instance()->get('theme');
        echo $view->renderView('Shop/Site/Views::checkout/payment_methods.php');
    }
    
    /**
     * Submits a completed cart checkout processing
     * 
     */
    public function submit()
    {
        $cart = \Shop\Models\Carts::fetch();
        if ($cart->quantity() <= 0)
        {
            $this->app->reroute('/shop/cart');
        }
        
        $identity = $this->getIdentity();
        if (empty( $identity->id ))
        {
            $flash = \Dsc\Flash::instance();
            \Base::instance()->set('flash', $flash );
            
            $this->app->set('meta.title', 'Login or Register | Checkout');
        
            $view = \Dsc\System::instance()->get( 'theme' );
            echo $view->render( 'Shop/Site/Views::checkout/identity.php' );
            return;
        }
                
        $f3 = \Base::instance();

        // Update the cart with checkout data from the form
        $checkout_inputs = $this->input->get( 'checkout', array(), 'array' );
        if (!empty($checkout_inputs['billing_address']['same_as_shipping'])) {
            $checkout_inputs['billing_address']['same_as_shipping'] = true;
        } else {
            $checkout_inputs['billing_address']['same_as_shipping'] = false;
        }
        $cart_checkout = array_merge( (array) $cart->{'checkout'}, $checkout_inputs );
        $cart->checkout = $cart_checkout;
        $cart->save();        
        
        // Get \Shop\Models\Checkout
            // Bind the cart and payment data to the checkout model
        $checkout = \Shop\Models\Checkout::instance();
        $checkout->addCart($cart)->addPaymentData($f3->get('POST'));
        
        // Fire a beforeShopCheckout event that allows Listeners to hijack the checkout process
        // Payment processing & authorization could occur at this event, and the Listener would update the checkout object
            // Add the checkout model to the event
        $event = new \Joomla\Event\Event( 'beforeShopCheckout' );
        $event->addArgument('checkout', $checkout);
        
        try {
            $event = \Dsc\System::instance()->getDispatcher()->triggerEvent($event);
        }
        catch (\Exception $e) {
            $checkout->setError( $e->getMessage() );
            $event->setArgument('checkout', $checkout);
        }
        
        $checkout = $event->getArgument('checkout');

        // option 1: ERRORS in checkout from beforeShopCheckout        
        if (!empty($checkout->getErrors())) 
        {
            // Add the errors to the stack and redirect
            foreach ($checkout->getErrors() as $exception) 
            {
            	\Dsc\System::addMessage( $exception->getMessage(), 'error' );
            }
            
            // redirect to the ./shop/checkout/payment page unless a failure redirect has been set in the session (site.shop.checkout.redirect.fail)
            $redirect = '/shop/checkout/payment';
            if ($custom_redirect = \Dsc\System::instance()->get( 'session' )->get( 'site.shop.checkout.redirect.fail' ))
            {
                $redirect = $custom_redirect;
            }
            
            \Dsc\System::instance()->get( 'session' )->set( 'site.shop.checkout.redirect.fail', null );
            $f3->reroute( $redirect );
            
            return;
        }
        
        // option 2: NO ERROR in checkout from beforeShopCheckout
        
        // If checkout is not completed, do the standard checkout process
        // If checkout was completed by a Listener during the beforeShopCheckout process, skip the standard checkout process and go to the afterShopCheckout event
        if (!$checkout->orderAccepted()) 
        {
            // the standard checkout process
            try {
                // failed payment processing should throw an exception
                $checkout->processPayment();
            } 
            catch (\Exception $e) 
            {
                \Dsc\System::addMessage( $e->getMessage(), 'error' );
                
                // redirect to the ./shop/checkout/payment page unless a failure redirect has been set in the session (site.shop.checkout.redirect.fail)
                $redirect = '/shop/checkout/payment';
                if ($custom_redirect = \Dsc\System::instance()->get( 'session' )->get( 'site.shop.checkout.redirect.fail' ))
                {
                    $redirect = $custom_redirect;
                }
                \Dsc\System::instance()->get( 'session' )->set( 'site.shop.checkout.redirect.fail', null );
                $this->app->reroute( $redirect );
                
                return;                
            }

            try {
                $checkout->acceptOrder();
            } catch (\Exception $e) {
                $checkout->setError( $e->getMessage() );
            }
            
            if (!$checkout->orderAccepted() || !empty($checkout->getErrors()))
            {
                \Dsc\System::addMessage( 'Checkout could not be completed.  Please try again or contact us if you have further difficulty.', 'error' );
                
                // Add the errors to the stack and redirect
                foreach ($checkout->getErrors() as $exception)
                {
                    \Dsc\System::addMessage( $exception->getMessage(), 'error' );
                }
            
                // redirect to the ./shop/checkout/payment page unless a failure redirect has been set in the session (site.shop.checkout.redirect.fail)
                $redirect = '/shop/checkout/payment';
                if ($custom_redirect = \Dsc\System::instance()->get( 'session' )->get( 'site.shop.checkout.redirect.fail' ))
                {
                    $redirect = $custom_redirect;
                }
            
                \Dsc\System::instance()->get( 'session' )->set( 'site.shop.checkout.redirect.fail', null );
                $f3->reroute( $redirect );
            
                return;
            }        
        }
        
        // the order WAS accepted
        // Fire an afterShopCheckout event
        $event_after = new \Joomla\Event\Event( 'afterShopCheckout' );
        $event_after->addArgument('checkout', $checkout);
        
        try {
            $event_after = \Dsc\System::instance()->getDispatcher()->triggerEvent($event_after);
        } catch (\Exception $e) {
            \Dsc\System::addMessage( $e->getMessage(), 'warning' );
        }
        
        // Redirect to ./shop/checkout/confirmation unless a site.shop.checkout.redirect has been set
        $redirect = '/shop/checkout/confirmation';
        if ($custom_redirect = \Dsc\System::instance()->get( 'session' )->get( 'site.shop.checkout.redirect' ))
        {
            $redirect = $custom_redirect;
        }
        
        \Dsc\System::instance()->get( 'session' )->set( 'site.shop.checkout.redirect', null );
        $f3->reroute( $redirect );
        
        return;
    }
    
    public function register()
    {
        $f3 = \Base::instance();
        
        $checkout_method = strtolower( $this->input->get( 'checkout_method', null, 'alnum' ) );
        switch ($checkout_method) 
        {
            // if $checkout_method == guest
            // store email in cart object and then continue
            // create a guest mongoid
        	case "guest":
        	    
        	    $real_email = trim( strtolower( $this->input->get( 'email_address', null, 'string' ) ) );
        	    
        	    if (\Users\Models\Users::emailExists($real_email)) 
        	    {
        	        \Dsc\System::addMessage( 'This email is already registered. Please login to continue.  <a href="./user/forgot-password">If necessary, you can recover your password here.</a>', 'error' );
        	        $this->app->reroute( '/shop/checkout' );
        	        return;        	        
        	    }
        	    
        	    $mongo_id = (string) new \MongoId;        	    
        	    $email = 'guest-' . $mongo_id . '@' . $mongo_id . '.' . $mongo_id;
        	    $password = \Users\Models\Users::generateRandomString();
        	    
        	    $data = array(
        	        'first_name' => 'Guest',
        	        'last_name' => 'User',
        	        'email' => $email,
        	        'guest_email' => $real_email,
        	        'new_password' => $password,
        	        'confirm_new_password' => $password
        	    );
        	     
        	    $user = (new \Users\Models\Users)->bind($data);
        	    
        	    try
        	    {
        	        // this will handle other validations, such as username uniqueness, etc
        	        $user->guest = true;
        	        $user->active = false;
        	        $user->save();
        	    }
        	    catch(\Exception $e)
        	    {
        	        \Dsc\System::addMessage( 'Could not create guest account', 'error' );
        	        \Dsc\System::addMessage( $e->getMessage(), 'error' );
        	        \Dsc\System::instance()->setUserState('shop.checkout.register.flash_filled', true);
        	        $flash = \Dsc\Flash::instance();
        	        $flash->store(array());
        	        $this->app->reroute('/shop/checkout');
        	        return;
        	    }
        	    
        	    // if we have reached here, then all is right with the form
        	    $flash = \Dsc\Flash::instance();
        	    $flash->store(array());
        	    
        	    // login the user, trigger Listeners
        	    \Dsc\System::instance()->get( 'auth' )->login( $user );
        	     
        	    $this->app->reroute( '/shop/checkout' );        	    
        	    
        	    break;
            
            // if $checkout_method == register
            // validate data
            // create user
            // redirect back to checkout
    	    case "register":
    	        
    	        $email = trim( strtolower( $this->input->get( 'email_address', null, 'string' ) ) );
    	        
    	        $data = array(
    	            'first_name' => $this->input->get( 'first_name', null, 'string' ),
    	            'last_name' => $this->input->get( 'last_name', null, 'string' ),
    	            'email' => $email,
    	            'new_password' => $this->input->get( 'new_password', null, 'string' ),
    	            'confirm_new_password' => $this->input->get( 'confirm_new_password', null, 'string' )
    	        );
    	        
    	        $user = (new \Users\Models\Users)->bind($data);
    	        
    	        // Check if the email already exists and give a custom message if so
    	        if (!empty($user->email) && $existing = $user->emailExists( $user->email ))
    	        {
    	            if ((empty($user->id) || $user->id != $existing->id))
    	            {
    	                \Dsc\System::addMessage( 'This email is already registered.', 'error' );
    	                \Dsc\System::instance()->setUserState('shop.checkout.register.flash_filled', true);
    	                $flash = \Dsc\Flash::instance();
    	                $flash->store($user->cast());
    	                $this->app->reroute( '/shop/checkout' );    	        
    	                return;
    	            }
    	        }
    	        
    	        try
    	        {
    	            // this will handle other validations, such as username uniqueness, etc
    	            $settings = \Users\Models\Settings::fetch();
    	            $registration_action = $settings->{'general.registration.action'};    	            
    	            switch ($registration_action)
    	            {
    	            	case "auto_login":
    	            	    $user->active = true;
    	            	    $user->save();
    	            	    break;
    	            	case "auto_login_with_validation":
    	            	    $user->active = false;
    	            	    $user->save();
    	            	    $user->sendEmailValidatingEmailAddress();
    	            	    break;
    	            	default:
    	            	    $user->active = false;
    	            	    $user->save();
    	            	    $user->sendEmailValidatingEmailAddress();
    	            	    break;
    	            }    	            
    	        }
    	        catch(\Exception $e)
    	        {
    	            \Dsc\System::addMessage( 'Could not create account.', 'error' );
    	            \Dsc\System::addMessage( $e->getMessage(), 'error' );
    	            \Dsc\System::instance()->setUserState('shop.checkout.register.flash_filled', true);
    	            $flash = \Dsc\Flash::instance();
    	            $flash->store($user->cast());
    	            $f3->reroute('/shop/checkout');
    	            return;
    	        }
    	        
    	        // if we have reached here, then all is right with the form
    	        $flash = \Dsc\Flash::instance();
    	        $flash->store(array());    	  

    	        // login the user, trigger Listeners
    	        \Dsc\System::instance()->get( 'auth' )->login( $user );
    	        
    	        $this->app->reroute( '/shop/checkout' );
    	        
    	        break;
        	         
            // if $checkout_method something else,
            // add message?
            // redirect back to checkout
    	    default:
    	        \Dsc\System::addMessage( 'Invalid Checkout Method', 'error' );
    	        $this->app->reroute( '/shop/checkout' );
	            break;
	             
        }

    }
}