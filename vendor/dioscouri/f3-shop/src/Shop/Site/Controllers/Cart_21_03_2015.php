<?php
namespace Shop\Site\Controllers;

class Cart extends \Dsc\Controller
{

    /**
     * Display a user's cart
     */
    public function read()
    {
        $id = $this->inputfilter->clean($this->app->get('PARAMS.id'), 'alnum');
        $user_id = $this->input->get('user_id', '', 'alnum');
        
        // check, if we're not forcing user to view a certain cart (i. e. after they click on link in email)
        if (!empty($id))
        {
            $cart = (new \Shop\Models\Carts())->setState('filter.id', $id)->getItem();
            if (empty($cart))
            { 
                // this cart does not exist so let's not disclose that to people. rather, we say the cart is empty (which is true)
                $this->app->reroute('/shop/cart');
                return;
            }
            
            $identity = $this->getIdentity();
            
            if (empty($identity->id))
            {
                $token = $this->input->get('auto_login_token', '', 'alnum');
                
                if (empty($user_id) || empty($token))
                {
                    \Dsc\System::instance()->get('session')->set('site.login.redirect', '/shop/cart');
                    $this->app->reroute('/sign-in');
                }
                else
                {
                    // try to auto log in user
                    $notification_idx = $this->input->get("idx", 0, 'int');
                    $this->auth->loginWithToken($user_id, $token, '/shop/cart?email=1&user_id=' . $user_id . '&idx=' . $notification_idx);
                }
            }
            else
            {
                if ((string) $cart->user_id != (string) $identity->id)
                {
                    $this->app->reroute('/shop/cart');
                    return;
                }
            }
        }
        
        $referal_email = $this->input->get("email", 0, 'int');
        if ($referal_email)
        {
            $identity = $this->getIdentity();
            if ($identity->id == $user_id)
            {
                // only if the right user is logged in
                $notification_idx = $this->input->get("idx", 0, 'int');
                \Dsc\Activities::track('User clicked on link in abandoned cart email', array(
                    'Notification' => $notification_idx
                ));
                \Dsc\System::instance()->get('session')->set('shop.notification_email', 1);
            }
            $this->app->reroute('/shop/cart');
            return;
        }
        
        $cart = \Shop\Models\Carts::fetch();
        // Update product fields stored in cart
        foreach ($cart->validateProducts() as $change)
        {
            \Dsc\System::addMessage($change);
        }
        $cart->applyCredit();
        
        \Base::instance()->set('cart', $cart);
        
        $this->app->set('meta.title', 'Shopping Cart');
        
        \Shop\Models\Activities::track('Viewed Cart', array(
            'cart_id' => (string) $cart->id
        ));
        
        $view = \Dsc\System::instance()->get('theme');
        echo $view->renderTheme('Shop/Site/Views::cart/read.php');
    }

    /**
     * Add an item to a cart
     */
    public function add()
    {
        $redirect = '/shop/cart';
        if ($custom_redirect = \Dsc\System::instance()->get('session')->get('shop.add_to_cart.product.redirect'))
        {
            $redirect = $custom_redirect;
        }
        
        // -----------------------------------------------------
        // Start: validation
        // -----------------------------------------------------
        $variant_id = $this->input->get('variant_id');
        
        // load the product
        try
        {
            $product = (new \Shop\Models\Variants())->getById($variant_id);
        }
        catch (\Exception $e)
        {
            if ($this->app->get('AJAX'))
            {
                return $this->outputJson($this->getJsonResponse(array(
                    'result' => false
                )));
            }
            else
            {
                \Dsc\System::addMessage('Item not added to cart - Invalid product', 'error');
                $this->app->reroute($redirect);
                return;
            }
        }
        // -----------------------------------------------------
        // End: validation
        // -----------------------------------------------------
        
        // get the current user's cart, either based on session_id (visitor) or user_id (logged-in)
        $cart = \Shop\Models\Carts::fetch();
        
        // add the item
        try
        {
            $cart->addItem($variant_id, $product, $this->app->get('POST'));
        }
        catch (\Exception $e)
        {
            if ($this->app->get('AJAX'))
            {
                return $this->outputJson($this->getJsonResponse(array(
                    'result' => false
                )));
            }
            else
            {
                \Dsc\System::addMessage('Item not added to cart', 'error');
                \Dsc\System::addMessage($e->getMessage(), 'error');
                $this->app->reroute($redirect);
                return;
            }
        }
        
        // Track it
        if ($variant = $product->variant($variant_id))
        {
            \Shop\Models\Activities::track('Added to Cart', array(
                'SKU' => $product->{'tracking.sku'},
                'Variant Title' => !empty($variant['attribute_title']) ? $variant['attribute_title'] : $product->title,
                'Product Name' => $product->title,
                'variant_id' => (string) $variant_id,
                'product_id' => (string) $product->id
            ));
        }
        else
        {
            \Shop\Models\Activities::track('Added to Cart', array(
                'SKU' => $product->{'tracking.sku'},
                'Product Name' => $product->title,
                'variant_id' => (string) $variant_id,
                'product_id' => (string) $product->id
            ));
        }
        
        if ($this->app->get('AJAX'))
        {
            $this->app->set('cart', $cart);
            
            return $this->outputJson($this->getJsonResponse(array(
                'result' => true,
                'html' => $this->theme->renderView('Shop/Site/Views::cart/lightbox.php')
            )));
        }
        else
        {
            \Dsc\System::addMessage('Item added to cart.  <a href="./shop/checkout"><b>Click here to checkout now!</b></a>');
            $this->app->reroute($redirect);
        }
    }

    /**
     * Remove an item from the cart
     */
    public function remove()
    {
        // -----------------------------------------------------
        // Start: validation
        // -----------------------------------------------------
        // validate the POST values
        // min: cartitem_hash
        if (!$cartitem_hash = $this->inputfilter->clean($this->app->get('PARAMS.cartitem_hash'), 'cmd'))
        {
            // if validation fails, respond appropriately
            if ($this->app->get('AJAX'))
            {
                return $this->outputJson($this->getJsonResponse(array(
                    'result' => false
                )));
            }
            else
            {
                \Dsc\System::addMessage('Invalid Cart Item', 'error');
                $this->app->reroute('/shop/cart');
            }
        }
        
        // -----------------------------------------------------
        // End: validation
        // -----------------------------------------------------
        
        // get the current user's cart, either based on session_id (visitor) or user_id (logged-in)
        $cart = \Shop\Models\Carts::fetch();
        
        // remove the item
        try
        {
            $cart->removeItem($cartitem_hash);
            
            if ($this->app->get('AJAX'))
            {
                return $this->outputJson($this->getJsonResponse(array(
                    'result' => true
                )));
            }
            else
            {
                \Dsc\System::addMessage('Item removed from cart');
                $this->app->reroute('/shop/cart');
            }
        }
        catch (\Exception $e)
        {
            // respond appropriately with failure message
            if ($this->app->get('AJAX'))
            {
                return $this->outputJson($this->getJsonResponse(array(
                    'result' => false
                )));
            }
            else
            {
                \Dsc\System::addMessage($e->getMessage(), 'error');
                $this->app->reroute('/shop/cart');
            }
        }
    }

    /**
     * Update a cart
     */
    public function updateQuantities()
    {
        // get the current user's cart, either based on session_id (visitor) or user_id (logged-in)
        $cart = \Shop\Models\Carts::fetch();
        
        $quantities = $this->input->get('quantities', array(), 'array');
        foreach ($cart->items as $item)
        {
            if (isset($quantities[$item['hash']]))
            {
                $new_quantity = (int) $quantities[$item['hash']];
                if ($new_quantity < 1)
                {
                    $cart->removeItem($item['hash']);
                }
                else
                {
                    $cart->updateItemQuantity($item['hash'], $new_quantity);
                }
            }
        }
        
        if ($this->app->get('AJAX'))
        {
            return $this->outputJson($this->getJsonResponse(array(
                'result' => true
            )));
        }
        else
        {
            \Dsc\System::addMessage('Quantities updated');
            $this->app->reroute('/shop/cart');
        }
    }

    /**
     */
    public function addCoupon()
    {
        $redirect = '/shop/cart';
        if ($custom_redirect = \Dsc\System::instance()->get('session')->get('site.addcoupon.redirect'))
        {
            $redirect = $custom_redirect;
        }
        \Dsc\System::instance()->get('session')->set('site.addcoupon.redirect', null);
        
        // -----------------------------------------------------
        // Start: validation
        // -----------------------------------------------------
        $coupon_code = trim(strtolower($this->input->get('coupon_code', null, 'string')));
        
        try
        {
            if (empty($coupon_code))
            {
                throw new \Exception('Please provide a coupon code');
            }
            
            // load the coupon, and if it exists, try to add it to the cart
            $coupon = (new \Shop\Models\Coupons())->setState('filter.code', $coupon_code)->getItem();
            
            if (empty($coupon->id))
            {
                throw new \Exception('Invalid Coupon Code');
            }
            
            // are we using a generated code? or a primary code?
            if (strtolower($coupon->code) != $coupon_code)
            {
                $coupon->generated_code = $coupon_code;
            }
        }
        catch (\Exception $e)
        {
            if ($this->app->get('AJAX'))
            {
                return $this->outputJson($this->getJsonResponse(array(
                    'result' => false
                )));
            }
            else
            {
                \Dsc\System::addMessage($e->getMessage(), 'error');
                $this->app->reroute($redirect);
                return;
            }
        }
        // -----------------------------------------------------
        // End: validation
        // -----------------------------------------------------
        
        $cart = \Shop\Models\Carts::fetch();
        
        // -----------------------------------------------------
        // Start: add the item
        // -----------------------------------------------------
        try
        {
            $cart->addCoupon($coupon);
        }
        catch (\Exception $e)
        {
            if ($this->app->get('AJAX'))
            {
                return $this->outputJson($this->getJsonResponse(array(
                    'result' => false
                )));
            }
            else
            {
                \Dsc\System::addMessage('Discount not applied.', 'error');
                \Dsc\System::addMessage($e->getMessage(), 'error');
                $this->app->reroute($redirect);
                return;
            }
        }
        // -----------------------------------------------------
        // End: add the item
        // -----------------------------------------------------
        
        if ($this->app->get('AJAX'))
        {
            return $this->outputJson($this->getJsonResponse(array(
                'result' => true
            )));
        }
        else
        {
            \Dsc\System::addMessage('Added coupon: ' . $coupon_code);
            $this->app->reroute($redirect);
        }
    }

    /**
     * Remove an item from the cart
     */
    public function removeCoupon()
    {
        $redirect = '/shop/cart';
        if ($custom_redirect = \Dsc\System::instance()->get('session')->get('site.removecoupon.redirect'))
        {
            $redirect = $custom_redirect;
        }
        \Dsc\System::instance()->get('session')->set('site.removecoupon.redirect', null);
        
        // -----------------------------------------------------
        // Start: validation
        // -----------------------------------------------------
        // validate the POST values
        if (!$code = $this->inputfilter->clean($this->app->get('PARAMS.code'), 'string'))
        {
            // if validation fails, respond appropriately
            if ($this->app->get('AJAX'))
            {
                return $this->outputJson($this->getJsonResponse(array(
                    'result' => false
                )));
            }
            else
            {
                \Dsc\System::addMessage('Invalid Coupon Code', 'error');
                $this->app->reroute('/shop/cart');
            }
        }
        
        // -----------------------------------------------------
        // End: validation
        // -----------------------------------------------------
        
        // get the current user's cart, either based on session_id (visitor) or user_id (logged-in)
        $cart = \Shop\Models\Carts::fetch();
        
        // remove the item
        try
        {
            $cart->removeCoupon($code);
            
            if ($this->app->get('AJAX'))
            {
                return $this->outputJson($this->getJsonResponse(array(
                    'result' => true
                )));
            }
            else
            {
                \Dsc\System::addMessage('Coupon removed from cart');
                $this->app->reroute('/shop/cart');
            }
        }
        catch (\Exception $e)
        {
            if ($this->app->get('AJAX'))
            {
                return $this->outputJson($this->getJsonResponse(array(
                    'result' => false
                )));
            }
            else
            {
                \Dsc\System::addMessage($e->getMessage(), 'error');
                $this->app->reroute('/shop/cart');
            }
        }
    }

    /**
     */
    public function addGiftCard()
    {
        $redirect = '/shop/cart';
        if ($custom_redirect = \Dsc\System::instance()->get('session')->get('site.addgiftcard.redirect'))
        {
            $redirect = $custom_redirect;
        }
        \Dsc\System::instance()->get('session')->set('site.addgiftcard.redirect', null);
        
        // -----------------------------------------------------
        // Start: validation
        // -----------------------------------------------------
        $giftcard_code = trim($this->input->get('giftcard_code', null, 'alnum'));
        
        try
        {
            if (empty($giftcard_code))
            {
                throw new \Exception('Please provide a gift card code');
            }
            
            $regex = '/^[0-9a-z]{24}$/';
            if (!preg_match($regex, (string) $giftcard_code))
            {
                throw new \Exception('Please enter a valid gift card code');
            }
            
            // load the giftcard, and if it exists, try to add it to the cart
            $giftcard = (new \Shop\Models\OrderedGiftCards())->setState('filter.id', $giftcard_code)->getItem();
            if (empty($giftcard->id))
            {
                throw new \Exception('Invalid Gift Card Code');
            }
        }
        catch (\Exception $e)
        {
            if ($this->app->get('AJAX'))
            {
                return $this->outputJson($this->getJsonResponse(array(
                    'result' => false
                )));
            }
            else
            {
                \Dsc\System::addMessage($e->getMessage(), 'error');
                $this->app->reroute($redirect);
                return;
            }
        }
        // -----------------------------------------------------
        // End: validation
        // -----------------------------------------------------
        
        $cart = \Shop\Models\Carts::fetch();
        
        // -----------------------------------------------------
        // Start: add the item
        // -----------------------------------------------------
        try
        {
            $cart->addGiftcard($giftcard);
        }
        catch (\Exception $e)
        {
            if ($this->app->get('AJAX'))
            {
                return $this->outputJson($this->getJsonResponse(array(
                    'result' => false
                )));
            }
            else
            {
                \Dsc\System::addMessage('Gift card not added to cart', 'error');
                \Dsc\System::addMessage($e->getMessage(), 'error');
                $this->app->reroute($redirect);
                return;
            }
        }
        // -----------------------------------------------------
        // End: add the item
        // -----------------------------------------------------
        
        if ($this->app->get('AJAX'))
        {
            return $this->outputJson($this->getJsonResponse(array(
                'result' => true
            )));
        }
        else
        {
            \Dsc\System::addMessage('Added Gift Card');
            $this->app->reroute($redirect);
        }
    }

    /**
     * Remove an item from the cart
     */
    public function removeGiftCard()
    {
        $redirect = '/shop/cart';
        if ($custom_redirect = \Dsc\System::instance()->get('session')->get('site.removegiftcard.redirect'))
        {
            $redirect = $custom_redirect;
        }
        \Dsc\System::instance()->get('session')->set('site.removegiftcard.redirect', null);
        
        // -----------------------------------------------------
        // Start: validation
        // -----------------------------------------------------
        // validate the POST values
        if (!$code = $this->inputfilter->clean($this->app->get('PARAMS.code'), 'alnum'))
        {
            // if validation fails, respond appropriately
            if ($this->app->get('AJAX'))
            {
                return $this->outputJson($this->getJsonResponse(array(
                    'result' => false
                )));
            }
            else
            {
                \Dsc\System::addMessage('Invalid Gift Card', 'error');
                $this->app->reroute('/shop/cart');
            }
        }
        
        // -----------------------------------------------------
        // End: validation
        // -----------------------------------------------------
        
        // get the current user's cart, either based on session_id (visitor) or user_id (logged-in)
        $cart = \Shop\Models\Carts::fetch();
        
        // remove the item
        try
        {
            $cart->removeGiftCard($code);
            
            if ($this->app->get('AJAX'))
            {
                return $this->outputJson($this->getJsonResponse(array(
                    'result' => true
                )));
            }
            else
            {
                \Dsc\System::addMessage('Gift card removed from cart');
                $this->app->reroute($redirect);
            }
        }
        catch (\Exception $e)
        {
            if ($this->app->get('AJAX'))
            {
                return $this->outputJson($this->getJsonResponse(array(
                    'result' => false
                )));
            }
            else
            {
                \Dsc\System::addMessage($e->getMessage());
                $this->app->reroute($redirect);
            }
        }
    }
}