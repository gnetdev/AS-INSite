<?php
namespace Shop\Models;

/**
 * Process a checkout and mark it as completed if successful.
 * Processing a checkout means creating an order object.  
 * 
 * @author Rafael Diaz-Tushman
 *
 */
class Checkout extends \Dsc\Singleton
{
    protected $__orderAccepted = false;
    protected $__cart = null;             // \Shop\Models\Carts object
    protected $__paymentData = array();
    protected $__order = null;             // \Shop\Models\Carts object
    protected $__paymentMethod = null;
    protected $__paymentResult = null;
    protected $__validatePaymentResult = null;
    
    /**
     * Process a checkout and mark it as completed if successful.
     * Processing a checkout means creating an order object.  
     * 
     * @return \Shop\Models\Checkout
     */
    public function createOrder($refresh=false)
    {
        if (!empty($this->__order) && empty($refresh))
        {
             return $this;
        }
                
        // Convert the cart to an Order object
        $this->__order = \Shop\Models\Orders::fromCart( $this->cart() );
        
        // Add payment details if applicable
        // Don't add the credit card number form the PaymentData to the cart, it shouldn't be stored in the order object in the DB
        $payment_data = (array) $this->paymentData();
        \Dsc\ArrayHelper::clear( $payment_data, 'card' );
        
        $this->__order->addPayment( $payment_data );
        
        return $this;
    }
    
    public function acceptOrder()
    {
        if ($this->order()->save()) 
        {
            $this->order()->accept();
            $this->setOrderAccepted();

            // delete all scheduled abandoned cart notifications
            \Shop\Models\CartsAbandoned::deleteQueuedEmails( $this->cart() );
            $this->cart()->remove();
echo '<script type="text/javascript">
/* <![CDATA[ */
var google_conversion_id = 944232948;
var google_conversion_language = "en";
var google_conversion_format = "3";
var google_conversion_color = "ffffff";
var google_conversion_label = "xgGaCNfg2V4Q9LOfwgM";
var google_remarketing_only = false;
/* ]]> */
</script>
<script type="text/javascript" src="//www.googleadservices.com/pagead/conversion.js">
</script>
<noscript>
<div style="display:inline;">
<img height="1" width="1" style="border-style:none;" alt="" src="//www.googleadservices.com/pagead/conversion/944232948/?label=xgGaCNfg2V4Q9LOfwgM&amp;guid=ON&amp;script=0"/>
</div>
</noscript>';

echo '<!-- Facebook Conversion Code for Checkouts - Sekar Samy 1 -->
<script>(function() {
var _fbq = window._fbq || (window._fbq = []);
if (!_fbq.loaded) {
var fbds = document.createElement("script");
fbds.async = true;
fbds.src = "//connect.facebook.net/en_US/fbds.js";
var s = document.getElementsByTagName("script")[0];
s.parentNode.insertBefore(fbds, s);
_fbq.loaded = true;
}
})();
window._fbq = window._fbq || [];
window._fbq.push([\'track\', \'6028574031157\', {\'value\':\'0.00\',\'currency\':\'INR\'}]);
</script>
<noscript><img height="1" width="1" alt="" style="display:none" src="https://www.facebook.com/tr?ev=6028574031157&amp;cd[value]=0.00&amp;cd[currency]=INR&amp;noscript=1" /></noscript>';

                        
            \Dsc\System::instance()->get('session')->set('shop.just_completed_order', true );
            \Dsc\System::instance()->get('session')->set('shop.just_completed_order_id', (string) $this->__order->id );
        }

        return $this;
    }
    
    /**
     * 
     */
    public function order($refresh=false)
    {
        if (empty($this->__order) || $refresh)
        {
            $this->createOrder($refresh);
        }
                
        return $this->__order;
    }
    
    /**
     * Determine if the checkout has been completed.
     * Being completed means an order has been created for the checkout(cart+payment) 
     * 
     * @return boolean
     */
    public function orderAccepted()
    {
        return true === $this->__orderAccepted;
    }
    
    /**
     * Mark the checkout as complete, which means the order has been created for the cart+payment
     */
    public function setOrderAccepted()
    {
        // TODO before and after Events?
        
        $this->__orderAccepted = true;
        
        return $this;
    }
    
    /**
     * Add cart data to the checkout model
     * 
     * @param \Shop\Models\Carts $cart
     * @return \Shop\Models\Checkout
     */
    public function addCart(\Shop\Models\Carts $cart) 
    {
        // TODO set model properties from cart
        $this->__cart = $cart;
        
    	return $this;
    }
    
    /**
     * Get the cart used for checkout
     */
    public function cart()
    {
        // TODO Handle when $this->__cart is empty.  throw error?
        
        return $this->__cart;
    }
    
    /**
     * Add payment data to the checkout model
     * 
     * @param array $data
     * @return \Shop\Models\Checkout
     */
    public function addPaymentData(array $data)
    {
    	$this->__paymentData = $data;
    	
    	return $this;
    }
    
    /**
     * Get the payment data used for checkout
     */
    public function paymentData()
    {
        // TODO Handle when $this->__paymentData is empty.  throw error?
    
        return $this->__paymentData;
    }
    
    /**
     * What is the payment method selected for this checkout?
     * 
     */
    public function paymentMethodId()
    {
        $paymentData = $this->paymentData();
        
        if (!empty($paymentData['payment_method'])) 
        {
            return $paymentData['payment_method'];
        }
        
        return null;
    }
    
    /**
     * 
     * @throws \Exception
     * @return unknown
     */
    public function paymentMethod()
    {
        if (!empty($this->__paymentMethod)) 
        {
            return $this->__paymentMethod;
        }
        
        if (!$payment_method = $this->paymentMethodId())
        {
            throw new \Exception('Missing payment method');
        }
        
        $pm = (new \Shop\Models\PaymentMethods)->setState('filter.identifier', $payment_method)->setState('filter.enabled', true)->getItem();
        
        if (empty($pm->id))
        {
            throw new \Exception('Invalid payment method');
        }
        
        $this->__paymentMethod = $pm;
        
        return $this->__paymentMethod;        
    }    
    
    /**
     * Process the checkout payment data
     * 
     * Throws an \Exception
     * 
     * @return \Shop\Models\Checkout
     */
    public function processPayment()
    {
        if (!$this->cart()->paymentRequired()) 
        {
            return $this;
        }
        
        $paymentMethod = $this->paymentMethod();
        
        $this->__paymentResult = $paymentMethod->addCart( $this->__cart )->addPaymentData( $this->paymentData() )->getClass()->processPayment();
        
        return $this;
    }
    
    /**
     *
     * @return NULL
     */
    public function paymentResult()
    {
        if (!empty($this->__paymentResult))
        {
            return $this->__paymentResult;
        }
    
        return null;
    }
    
    /**
     * Validate the checkout payment data
     *
     * Throws an \Exception
     *
     * @return \Shop\Models\Checkout
     */
    public function validatePayment()
    {
        if (!$this->cart()->paymentRequired())
        {
            return $this;
        }
                
        $paymentMethod = $this->paymentMethod();
    
        $order = $this->order();
        $cart = $this->cart();
        
        $this->__validatePaymentResult = $paymentMethod->addCheckout( $this )->addOrder( $order )->addCart( $cart )->addPaymentData( $this->paymentData() )->getClass()->validatePayment();
    
        return $this;
    }

    /**
     *
     * @return NULL
     */
    public function validatePaymentResult()
    {
        if (!empty($this->__validatePaymentResult))
        {
            return $this->__validatePaymentResult;
        }
    
        return null;
    }
}