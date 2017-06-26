<?php
namespace Amrita\Site\Listeners;

class Shop extends \Prefab
{

    /**
     * Modifies the conditions clause in a query when a Collections view is loaded on the front-end of the site
     *
     * @param unknown $event            
     */
    public function ShopModelsCollections_getProductQueryConditions($event)
    {
        $collection = $event->getArgument('collection');
        $conditions = $event->getArgument('conditions');
        
        // check the collection for any amrita.whatever values and set the $conditions accordingly
        if (!empty($collection->{'amrita.type'}))
        {
            $types = array();
            foreach ($collection->{'amrita.type'} as $type)
            {
                $types[] = $type;
            }
            
            $conditions = array_merge($conditions, array(
                'amrita.type' => array(
                    '$in' => $types
                )
            ));
        }
        
        // add the conditions back to the event
        $event->setArgument('conditions', $conditions);
    }

    /**
     * Triggered when a cart calculates its taxes
     *
     * @param unknown $event            
     */
    public function onFetchTaxItemsForCart($event)
    {
        $cart = $event->getArgument('cart');
        $taxes = $event->getArgument('taxes');
        
        // by default, the order is not taxable,
        // TODO Set the tax == to the Not Taxable item (-7 tax code)
        $taxes = array();
        $product_taxitem = null;
        $shipping_taxitem = null;
        
        $user = (new \Users\Models\Users())->setState('filter.id', $cart->{'user_id'})->getItem();
        
        $is_wholesale = false;
        foreach ((array) $user->{'groups'} as $group)
        {
            if ($group['slug'] == 'wholesale')
            {
                $is_wholesale = true;
            }
        }
        
        if ($is_wholesale == false)
        {
            // TODO Based on the shipping address, get the tax line items for the cart
            if ($cart->{'checkout.shipping_address.country'} == 'US')
            {
                switch ($cart->{'checkout.shipping_address.region'})
                {
                    case "NY":
                        $product_taxitem = new \Shop\Models\Prefabs\TaxItems();
                        $product_taxitem->name = 'NY Sales Tax';
                        $product_taxitem->type = 'product';
                        $product_taxitem->rate = '0.0875'; // 8.875%
                        
                        $shipping_taxitem = new \Shop\Models\Prefabs\TaxItems();
                        $shipping_taxitem->name = 'NY Sales Tax on Shipping';
                        $shipping_taxitem->type = 'shipping';
                        $shipping_taxitem->rate = '0.0875'; // 8.875%
                        break;
                    case "NJ":
                        $product_taxitem = new \Shop\Models\Prefabs\TaxItems();
                        $product_taxitem->name = 'NJ Sales Tax';
                        $product_taxitem->type = 'product';
                        $product_taxitem->rate = '0.07'; // 7%
                        
                        $shipping_taxitem = new \Shop\Models\Prefabs\TaxItems();
                        $shipping_taxitem->name = 'NJ Sales Tax on Shipping';
                        $shipping_taxitem->type = 'shipping';
                        $shipping_taxitem->rate = '0.07'; // 7%
                        break;
                    default:
                        break;
                }
                
                if (!empty($product_taxitem->rate))
                {
                    // $cart->taxableTotal() includes shipping, so this covers all taxes
                    $total = $cart->taxableTotal();
                    $product_taxitem->total = $product_taxitem->rate * $total;
                    if ($product_taxitem->total <= 0)
                    {
                        $product_taxitem->total = 0;
                        $product_taxitem->rate = 0;
                    }
                }
            }
        }
        
        // add the tax items
        if (!empty($product_taxitem))
        {
            $taxes[] = $product_taxitem->cast();
        }
        
        // $cart->taxableTotal() includes shipping, so $product_taxitem covers all taxes
        if (!empty($shipping_taxitem))
        {
            // $taxes[] = $shipping_taxitem->cast();
        }
        
        $event->setArgument('taxes', $taxes);
        
        // stop event propogation
        $event->stop();
        
        return;
    }

    /**
     * Triggered when valid shipping method options are fetched for a cart
     *
     * @param unknown $event            
     */
    public function onFetchShippingMethodsForCart($event)
    {
        $cart = $event->getArgument('cart');
        
        $user = (new \Users\Models\Users())->setState('filter.id', $cart->{'user_id'})->getItem();
        $is_wholesale = false;
        foreach ((array) $user->{'groups'} as $group)
        {
            if ($group['slug'] == 'wholesale')
            {
                $is_wholesale = true;
            }
        }
        
        if ($is_wholesale) 
        {
            $this->wholesaleShippingMethods($event);
        }
        else 
        {
            $this->standardShippingMethods($event);
        }
    }

    /**
     * Set the shipping methods for a cart when the user is not a wholesaler
     * 
     * Include the corresponding Netsuite ID number for each of these shipping methods --
     * see Lists >> Accounting >> Shipping Items in the NS Interface
     *
     * @param unknown $event            
     */
    private function standardShippingMethods(&$event)
    {
        $cart = $event->getArgument('cart');
        $methods = $event->getArgument('methods');
        
        $item_surcharges = $cart->shippingSurchargeTotal();
        
        $total = $cart->subtotal() - $cart->giftCardTotal() - $cart->discountTotal() - $cart->creditTotal();
        
        // does the cart even require shipping?
        if ($cart->shippingRequired() !== true)
        {
            $method = new \Shop\Models\ShippingMethods(array(
                'id' => 'amrita.domestic.standard',
                'name' => 'No Shipping Required',
                'price' => '0.00',
                'extra' => $item_surcharges,
                'netsuite_id' => 14119
            ));
            $methods[] = $method->cast();
        }
        
        // India
        elseif ($cart->{'checkout.shipping_address.country'} == 'IN' || $cart->{'checkout.shipping_address.country'} == 'IND')
        {
            $settings = \Amrita\Models\Settings::fetch();
            
            if ($total > 1000)
            {
                $method = new \Shop\Models\ShippingMethods(array(
                    'id' => 'amrita.international.standard',
                    'name' => 'Standard',
                    'price' => $settings->{'india.shipping.over_1000'},
                    'extra' => $item_surcharges
                ));
                $methods[] = $method->cast();
            }
            elseif ($total > 500 && $total <= 1000)
            {
                $method = new \Shop\Models\ShippingMethods(array(
                    'id' => 'amrita.international.standard',
                    'name' => 'Standard',
                    'price' => $settings->{'india.shipping.500_1000'},
                    'extra' => $item_surcharges
                ));
                $methods[] = $method->cast();
            }
            else
            {
                $method = new \Shop\Models\ShippingMethods(array(
                    'id' => 'amrita.international.standard',
                    'name' => 'Standard',
                    'price' => $settings->{'india.shipping.under_500'},
                    'extra' => $item_surcharges
                ));
                $methods[] = $method->cast();
            }
        }
        
        // Is the shipping address in the continental US?
        elseif ($cart->{'checkout.shipping_address.country'} == 'US' && !in_array($cart->{'checkout.shipping_address.region'}, array(
            'AK',
            'HI',
            'PR'
        )))
        {
            $method = new \Shop\Models\ShippingMethods(array(
                'id' => 'amrita.domestic.standard',
                'name' => 'Standard (5-7 Business Days)',
                'price' => '4.95',
                'extra' => $item_surcharges,
                'netsuite_id' => 14119
            ));
            $methods[] = $method->cast();
            
            $method = new \Shop\Models\ShippingMethods(array(
                'id' => 'amrita.domestic.2_day',
                'name' => 'U.S. 2nd Day (2 Business Days)',
                'price' => '25.00',
                'extra' => $item_surcharges,
                'netsuite_id' => 14120
            ));
            $methods[] = $method->cast();
            
            $method = new \Shop\Models\ShippingMethods(array(
                'id' => 'amrita.domestic.1_day',
                'name' => 'U.S. Overnight (1 Business Day)',
                'price' => '35.00',
                'extra' => $item_surcharges,
                'netsuite_id' => 14121
            ));
            $methods[] = $method->cast();
        }
        
        // is the shipping address in Alaska, Hawaii, or Puerto Rico?
        // Order Total $ | Shipping Rates
        // $0 - $99 | $15
        // $100 - $149 | $10
        // $150+ | $5
        elseif (in_array($cart->{'checkout.shipping_address.country'}, array(
            'US',
            'PR'
        )) && in_array($cart->{'checkout.shipping_address.region'}, array(
            'AK',
            'HI',
            'PR'
        )))
        {
            if ($total > 150)
            {
                $method = new \Shop\Models\ShippingMethods(array(
                    'id' => 'amrita.domestic_non-continental.standard',
                    'name' => 'Standard',
                    'price' => '5.00',
                    'extra' => $item_surcharges,
                    'netsuite_id' => 14604
                ));
                $methods[] = $method->cast();
            }
            elseif ($total > 100 && $total < 150)
            {
                $method = new \Shop\Models\ShippingMethods(array(
                    'id' => 'amrita.domestic_non-continental.standard',
                    'name' => 'Standard',
                    'price' => '10.00',
                    'extra' => $item_surcharges,
                    'netsuite_id' => 14604
                ));
                $methods[] = $method->cast();
            }
            else
            {
                $method = new \Shop\Models\ShippingMethods(array(
                    'id' => 'amrita.domestic_non-continental.standard',
                    'name' => 'Standard',
                    'price' => '15.00',
                    'extra' => $item_surcharges,
                    'netsuite_id' => 14604
                ));
                $methods[] = $method->cast();
            }
        }
        
        // Australia
        elseif ($cart->{'checkout.shipping_address.country'} == 'AU')
        {
            $method = new \Shop\Models\ShippingMethods(array(
                'id' => 'amrita.international.standard',
                'name' => 'Standard (2-3 weeks)',
                'price' => '40.00',
                'extra' => $item_surcharges,
                'netsuite_id' => 17160
            ));
            $methods[] = $method->cast();
        }

        // Singapore
        elseif ($cart->{'checkout.shipping_address.country'} == 'SG')
        {
            $method = new \Shop\Models\ShippingMethods(array(
                'id' => 'amrita.international.standard',
                'name' => 'Standard (1-2 weeks)',
                'price' => '25.00',
                'extra' => $item_surcharges,
                'netsuite_id' => 17221
            ));
            $methods[] = $method->cast();
        }
        
        // all other international orders
        elseif ($cart->{'checkout.shipping_address.country'} != 'US')
        {
            if ($total >= 300)
            {
                $method = new \Shop\Models\ShippingMethods(array(
                    'id' => 'amrita.international.standard',
                    'name' => 'Standard (2-3 weeks)',
                    'price' => '10.00',
                    'extra' => $item_surcharges,
                    'netsuite_id' => 12789
                ));
                $methods[] = $method->cast();
                
                $method = new \Shop\Models\ShippingMethods(array(
                    'id' => 'amrita.international.express',
                    'name' => 'Express (7-10 days)',
                    'price' => '20.00',
                    'extra' => $item_surcharges,
                    'netsuite_id' => 15683
                ));
                $methods[] = $method->cast();
            }
            elseif ($total > 200 && $total < 300)
            {
                $method = new \Shop\Models\ShippingMethods(array(
                    'id' => 'amrita.international.standard',
                    'name' => 'Standard (2-3 weeks)',
                    'price' => '15.00',
                    'extra' => $item_surcharges,
                    'netsuite_id' => 12789
                ));
                $methods[] = $method->cast();
                
                $method = new \Shop\Models\ShippingMethods(array(
                    'id' => 'amrita.international.express',
                    'name' => 'Express (7-10 days)',
                    'price' => '25.00',
                    'extra' => $item_surcharges,
                    'netsuite_id' => 15683
                ));
                $methods[] = $method->cast();
            }
            elseif ($total > 150 && $total < 200)
            {
                $method = new \Shop\Models\ShippingMethods(array(
                    'id' => 'amrita.international.standard',
                    'name' => 'Standard (2-3 weeks)',
                    'price' => '20.00',
                    'extra' => $item_surcharges,
                    'netsuite_id' => 12789
                ));
                $methods[] = $method->cast();
                
                $method = new \Shop\Models\ShippingMethods(array(
                    'id' => 'amrita.international.express',
                    'name' => 'Express (7-10 days)',
                    'price' => '30.00',
                    'extra' => $item_surcharges,
                    'netsuite_id' => 15683
                ));
                $methods[] = $method->cast();
            }
            elseif ($total > 75 && $total < 150)
            {
                $method = new \Shop\Models\ShippingMethods(array(
                    'id' => 'amrita.international.standard',
                    'name' => 'Standard (2-3 weeks)',
                    'price' => '25.00',
                    'extra' => $item_surcharges,
                    'netsuite_id' => 12789
                ));
                $methods[] = $method->cast();
                
                $method = new \Shop\Models\ShippingMethods(array(
                    'id' => 'amrita.international.express',
                    'name' => 'Express (7-10 days)',
                    'price' => '35.00',
                    'extra' => $item_surcharges,
                    'netsuite_id' => 15683
                ));
                $methods[] = $method->cast();
            }
            else
            {
                $method = new \Shop\Models\ShippingMethods(array(
                    'id' => 'amrita.international.standard',
                    'name' => 'Standard (2-3 weeks)',
                    'price' => '30.00',
                    'extra' => $item_surcharges,
                    'netsuite_id' => 12789
                ));
                $methods[] = $method->cast();
                
                $method = new \Shop\Models\ShippingMethods(array(
                    'id' => 'amrita.international.express',
                    'name' => 'Express (7-10 days)',
                    'price' => '40.00',
                    'extra' => $item_surcharges,
                    'netsuite_id' => 15683
                ));
                $methods[] = $method->cast();
            }
        }
        
        $event->setArgument('methods', $methods);
        
        return;
    }

    /**
     * Set the shipping methods for a cart when the user is a wholesaler
     * 
     * Include the corresponding Netsuite ID number for each of these shipping methods --
     * see Lists >> Accounting >> Shipping Items in the NS Interface
     *
     * @param unknown $event            
     */
    private function wholesaleShippingMethods(&$event)
    {
        $cart = $event->getArgument('cart');
        $methods = $event->getArgument('methods');
        
        $item_surcharges = $cart->shippingSurchargeTotal();
                
        $total = $cart->subtotal() - $cart->giftCardTotal() - $cart->discountTotal() - $cart->creditTotal();
        
        // does the cart even require shipping?
        if ($cart->shippingRequired() !== true)
        {
            $method = new \Shop\Models\ShippingMethods(array(
                'id' => 'amrita.domestic.wholesale',
                'name' => 'No Shipping Required',
                'price' => '0.00',
                'extra' => $item_surcharges,
                'netsuite_id' => 9707
            ));
            $methods[] = $method->cast();
        }
        
        // India
        elseif ($cart->{'checkout.shipping_address.country'} == 'IN' || $cart->{'checkout.shipping_address.country'} == 'IND')
        {
            if ($total > 1000)
            {
                $method = new \Shop\Models\ShippingMethods(array(
                    'id' => 'amrita.international.standard',
                    'name' => 'Standard',
                    'price' => '0',
                    'extra' => $item_surcharges
                ));
                $methods[] = $method->cast();
            }
            elseif ($total > 500 && $total <= 1000)
            {
                $method = new \Shop\Models\ShippingMethods(array(
                    'id' => 'amrita.international.standard',
                    'name' => 'Standard',
                    'price' => '99',
                    'extra' => $item_surcharges
                ));
                $methods[] = $method->cast();
            }
            else
            {
                $method = new \Shop\Models\ShippingMethods(array(
                    'id' => 'amrita.international.standard',
                    'name' => 'Standard',
                    'price' => '49',
                    'extra' => $item_surcharges
                ));
                $methods[] = $method->cast();
            }
        }
        
        // Is the shipping address in the continental US?
        elseif ($cart->{'checkout.shipping_address.country'} == 'US' && !in_array($cart->{'checkout.shipping_address.region'}, array(
            'AK',
            'HI',
            'PR'
        )))
        {
            
            if ($total >= 500)
            {
                $method = new \Shop\Models\ShippingMethods(array(
                    'id' => 'amrita.domestic.wholesale',
                    'name' => 'Standard Wholesale',
                    'price' => '25.00',
                    'extra' => $item_surcharges,
                    'netsuite_id' => 9707
                ));
                $methods[] = $method->cast();
            
            }
            elseif ($total >= 200 && $total < 500)
            {
                $method = new \Shop\Models\ShippingMethods(array(
                    'id' => 'amrita.domestic.wholesale',
                    'name' => 'Standard Wholesale',
                    'price' => '20.00',
                    'extra' => $item_surcharges,
                    'netsuite_id' => 9707
                ));
                $methods[] = $method->cast();
            
            }
            else
            {
                $method = new \Shop\Models\ShippingMethods(array(
                    'id' => 'amrita.domestic.wholesale',
                    'name' => 'Standard Wholesale',
                    'price' => '15.00',
                    'extra' => $item_surcharges,
                    'netsuite_id' => 9707
                ));
                $methods[] = $method->cast();
            
            }            
        }

        // All orders outside the continental US ship for $40
        else 
        {
            $method = new \Shop\Models\ShippingMethods(array(
                'id' => 'amrita.international.wholesale',
                'name' => 'Wholesale - International',
                'price' => '40.00',
                'extra' => $item_surcharges,
                'netsuite_id' => 14125
            ));
            $methods[] = $method->cast();
        }
        
        $event->setArgument('methods', $methods);
        
        return;
    }

    /**
     * TODO Move this to the Netsuite Listener?
     * Or just reference a 'submit order' method in f3-netsuite?
     *
     * @param unknown $event            
     */
    public function beforeShopCheckout($event)
    {
        return null;
        
        if (\Base::instance()->get('DEBUG')) {
            return null;
        }
        
        $checkout = $event->getArgument('checkout');
        $payment_data = $checkout->paymentData();
        $order = $checkout->order();
        
        // accept all orders
        // \Dsc\System::addMessage('For debugging purposes, the Amrita Shop Listener is approving all orders without making requests to Netsuite', 'warn');
        // $order->financial_status = \Shop\Constants\OrderFinancialStatus::paid;
        // $checkout->acceptOrder();
        // return;
        
        // decline all orders
        // \Dsc\System::addMessage('For debugging purposes, the Amrita Shop Listener is declining all orders without making requests to Netsuite', 'warn');
        // $message = 'Your payment has been rejected. Please try another card.';
        // $checkout->setError( $message );
        // return;
        
        // Fuck Netsuite. Handle the "Only one request may be made against a session at a time" error
        $attempt = true;
        while ($attempt)
        {
            set_time_limit(0);
            
            try
            {
                // populate the order, creating customer + addresses if necessary
                $salesOrder = \Amrita\Models\SalesOrder::fromShopCheckout($checkout);
                
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
                    $checkout->setError($e->getMessage());
                    $event->setArgument('checkout', $checkout);
                    $order->setError($e->getMessage())
                        ->set('errors', $order->getErrors())
                        ->fail();
                    $attempt = false;
                    return;
                }
            }
        }
        
        // if order creation fails in netsuite, say so
        if (!$addResponse->writeResponse->status->isSuccess)
        {
            switch (trim($addResponse->writeResponse->status->statusDetail[0]->message))
            {
                case strpos($addResponse->writeResponse->status->statusDetail[0]->message, "Invalid account number") !== false:
                    // "Invalid account number. Possible action: Request a different card or other form of payment.":
                    $message = 'This card number is not recognized. Please confirm your billing information or use a different form of payment.';
                    break;
                case strpos($addResponse->writeResponse->status->statusDetail[0]->message, "General decline of the card") !== false:
                    // "General decline of the card. No other information provided by the issuing bank. Possible action: Request a different card or other form of payment.":
                    $message = 'The billing information that you entered does not match your credit card information. Please confirm your billing information or use a different form of payment.';
                    break;
                default:
                    // $message = $addResponse->writeResponse->status->statusDetail[0]->message;
                    $message = 'There was an error submitting your order.  Please try again.';
                    break;
            }
            
            try
            {
                $checkout->setError($message);
                $order->setError($message)
                    ->set('errors', $order->getErrors())
                    ->fail();
            }
            catch (\Exception $e)
            {
                $order->log($e->getMessage(), 'ERROR', __CLASS__ . '::' . __METHOD__);
            }
        }
        
        // else, order creation was successful
        else
        {
            $netsuite_order_id = $addResponse->writeResponse->baseRef->internalId;
            $order->set('netsuite', array(
                'id' => $netsuite_order_id
            ));
            
            // Now, get the order's details from netsuite. they don't respond with the order details in the ->add() request
            try
            {
                // get the order from netsuite
                $nsOrder = \Netsuite\Models\SalesOrder::fetchById($netsuite_order_id);
                $order->number = $nsOrder->tranId;
                
                // Set the order's payment method, using the last 4 from the customer's CC
                $order->payment_method = (new \Shop\Models\PaymentMethods(array(
                    'name' => 'Credit Card | ' . @$nsOrder->ccNumber
                )))->cast();
                
                switch ($nsOrder->orderStatus)
                {
                    case \SalesOrderOrderStatus::_closed:
                        $order->status = \Shop\Constants\OrderStatus::closed;
                        // $order->save();
                        
                        $message = 'Your card has been declined.  Please try another card.';
                        $checkout->setError($message);
                        $order->setError($message)
                            ->set('errors', $order->getErrors())
                            ->fail();
                        
                        $deleted_netsuite_order_id = \Netsuite\Models\SalesOrder::delete($netsuite_order_id);
                        
                        break;
                    case \SalesOrderOrderStatus::_cancelled:
                        $order->financial_status = \Shop\Constants\OrderFinancialStatus::voided;
                        $order->status = \Shop\Constants\OrderStatus::cancelled;
                        // $order->save();
                        /**
                         * [ccApproved] => 1
                         * [getAuth] =>
                         * [authCode] => 888888
                         * [ccAvsStreetMatch] => _y
                         * [ccAvsZipMatch] => _n
                         * [ccSecurityCodeMatch] => _y
                         * [altSalesTotal] =>
                         * [ignoreAvs] =>
                         * [paymentEventResult] => _accept
                         * [paymentEventHoldReason] =>
                         * [paymentEventType] => _authorizationRequest
                         * [paymentEventDate] => 2014-04-29T07:04:56.000-07:00
                         */
                        
                        $message = 'Your card has been declined. ';
                        if ($nsOrder->ccAvsStreetMatch == '_n' || $nsOrder->ccAvsZipMatch == '_n')
                        {
                            $message = 'The billing information that you entered does not match your credit card information. Please confirm the Billing Address you have provided.';
                        }
                        
                        if ($nsOrder->ccSecurityCodeMatch == '_n')
                        {
                            $message = 'The billing information that you entered does not match your credit card information. Please confirm the Security Code you have provided.';
                        }
                        
                        $message = trim($message);
                        $checkout->setError($message);
                        $order->setError($message)
                            ->set('errors', $order->getErrors())
                            ->fail();
                        
                        $deleted_netsuite_order_id = \Netsuite\Models\SalesOrder::delete($netsuite_order_id);
                        
                        break;
                    case \SalesOrderOrderStatus::_partiallyFulfilled:
                        $order->fulfillment_status = \Shop\Constants\OrderFulfillmentStatus::partial;
                        $checkout->acceptOrder();
                        break;
                    case \SalesOrderOrderStatus::_pendingBillingPartFulfilled:
                        $order->fulfillment_status = \Shop\Constants\OrderFulfillmentStatus::partial;
                        $checkout->acceptOrder();
                        break;
                    case \SalesOrderOrderStatus::_pendingFulfillment:
                        
                        // Need to evaluate the $nsOrder->paymentEventResult
                        // it can be one of 4 values from \TransactionPaymentEventResult
                        // _accept ==> only accept these
                        // _holdOverride
                        // _paymentHold
                        // _reject
                        
                        switch ($nsOrder->paymentEventResult)
                        {
                            case \TransactionPaymentEventResult::_accept:
                                
                                $order->financial_status = \Shop\Constants\OrderFinancialStatus::paid;
                                $checkout->acceptOrder();
                                
                                break;
                            case \TransactionPaymentEventResult::_holdOverride:
                            case \TransactionPaymentEventResult::_paymentHold:
                                
                                // Some orders on payment hold get rejected
                                if (!empty($nsOrder->authCode) && strtolower($nsOrder->authCode) == 'no auth code')
                                {
                                    // this happens for expired credit cards
                                    $order->financial_status = \Shop\Constants\OrderFinancialStatus::voided;
                                    $order->status = \Shop\Constants\OrderStatus::cancelled;
                                    // $order->save();
                                    $message = 'Your payment has been rejected.  Please try another card.';
                                    $checkout->setError($message);
                                    $order->setError($message)
                                        ->set('errors', $order->getErrors())
                                        ->fail();
                                    
                                    $deleted_netsuite_order_id = \Netsuite\Models\SalesOrder::delete($netsuite_order_id);
                                }
                                else
                                {
                                    $order->financial_status = \Shop\Constants\OrderFinancialStatus::pending;
                                    $checkout->acceptOrder();
                                }
                                break;
                            case \TransactionPaymentEventResult::_reject:
                                $order->financial_status = \Shop\Constants\OrderFinancialStatus::voided;
                                $order->status = \Shop\Constants\OrderStatus::cancelled;
                                // $order->save();
                                $message = 'Your payment has been rejected.  Please try another card.';
                                $checkout->setError($message);
                                $order->setError($message)
                                    ->set('errors', $order->getErrors())
                                    ->fail();
                                
                                $deleted_netsuite_order_id = \Netsuite\Models\SalesOrder::delete($netsuite_order_id);
                                
                                break;
                        }
                        
                        break;
                    case \SalesOrderOrderStatus::_fullyBilled:
                        $order->financial_status = \Shop\Constants\OrderFinancialStatus::paid;
                        $checkout->acceptOrder();
                        break;
                    case \SalesOrderOrderStatus::_pendingBilling:
                    case \SalesOrderOrderStatus::_pendingApproval:
                        $order->financial_status = \Shop\Constants\OrderFinancialStatus::pending;
                        $order->status = \Shop\Constants\OrderStatus::open;
                        $checkout->acceptOrder();
                        break;
                    case \SalesOrderOrderStatus::_undefined:
                        $message = 'Unknown error.  Please try again.';
                        $order->setError($message)
                            ->set('errors', $order->getErrors())
                            ->fail();
                        
                        $checkout->setError($message);
                        break;
                }
            }
            catch (\Exception $e)
            {
                $message = $e->getMessage();
                $checkout->setError($message);
                $order->log($e->getMessage(), 'ERROR', __CLASS__ . '::' . __METHOD__);
            }
        }
        
        $event->setArgument('checkout', $checkout);
        
        return;
    }

    public function afterShopCheckout($event)
    {
        $checkout = $event->getArgument('checkout');
        $cart = $checkout->cart();
        $order = $checkout->order();
        
        // push the task to netsuite asynchronously
        \Dsc\Queue::task('\Amrita\Models\ShopOrders::pushToNetsuite', array('id' => $order->id), array(
            'title' => 'Push order #'. $order->id .' to Netsuite for customer: ' . $order->user()->email
        ));        
        
        if ($cart->{'checkout.mailchimp_subscribe'})
        {
            if (!empty($order->user_email))
            {
                $settings = \Amrita\Models\Settings::fetch();
                $mailchimp_api_key = $settings->{'mailchimp.api_key'};
                $mailchimp_main_list_id = $settings->{'mailchimp.main_list_id'};
                if ($mailchimp_api_key && $mailchimp_main_list_id)
                {
                    $MailChimp = new \Drewm\MailChimp($mailchimp_api_key);
                    $result = $MailChimp->call('lists/subscribe', array(
                        'id' => $mailchimp_main_list_id,
                        'email' => array(
                            'email' => $order->user_email
                        ),
                        'merge_vars' => array(
                            'FNAME' => $order->{'user.first_name'},
                            'LNAME' => $order->{'user.last_name'}
                        ),
                        'double_optin' => false,
                        'update_existing' => true,
                        'replace_interests' => false,
                        'send_welcome' => false
                    ));
                    
                    if (!empty($result))
                    {
                        if (!empty($result->error))
                        {}
                        else
                        {
                            $user = $order->user();
                            
                            // update kissmetrics, if you can
                            $settings = \Admin\Models\Settings::fetch();
                            if (class_exists('\KM') && $settings->enabledIntegration('kissmetrics'))
                            {
                                \KM::init($settings->{'integration.kissmetrics.key'});
                                
                                \KM::identify($order->user_email);
                                \KM::record("Subscribed to Newsletter", array(
                                    'Newsletter Name' => "Main",
                                    'E-mail' => $order->user_email
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
}