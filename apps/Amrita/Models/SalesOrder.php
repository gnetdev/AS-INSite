<?php
namespace Amrita\Models;

class SalesOrder extends \Dsc\Models
{
    public static function taxItemFromAddress( array $address )
    {
        $taxItem = null;
        
        if (empty($address['region'])) {
        	return $taxItem;
        }
        
        switch ($address['region']) 
        {
        	case "NY":
        	    $taxItem = new \RecordRef();
        	    $taxItem->internalId = '2419'; // NY State salesTaxItem type, see Accounting >> Tax Codes
        	    $taxItem->type = \RecordType::salesTaxItem;        	    
        	    break;
        	case "NJ":
        	    $taxItem = new \RecordRef();
        	    $taxItem->internalId = '3508'; // NJ State salesTaxItem type, see Accounting >> Tax Codes
        	    $taxItem->type = \RecordType::salesTaxItem;        	    
        	    break;
        	default:
        	    /*
        	    $taxItem = new \RecordRef();
        	    $taxItem->internalId = '-7'; // Not Taxable salesTaxItem type, see Accounting >> Tax Codes
        	    $taxItem->type = \RecordType::salesTaxItem;
        	    */        	    
        	    break;
        }
        
        return $taxItem;
    }
    
    public static function fromShopCheckout( \Shop\Models\Checkout $checkout )
    {
        $service = \Netsuite\Factory::instance()->getService();
        
        $order = $checkout->order(); 
        $paymentData = $checkout->paymentData();
        
        $so = new \SalesOrder();
        
        $so->externalId = (string) $order->number; // 'RDT987654322'; // this is the tienda order id
        $so->department = new \RecordRef();
        $so->department->internalId = 1; // "Amrita Singh" dept -- see the Sales Order Form in the UI
        $so->class = new \RecordRef();
        $so->class->internalId = 3; // "Amrita Singh" class -- see the Sales Order Form in the UI
        $so->shipMethod = new \RecordRef();
        $so->shipMethod->internalId = 14119; // 14119 = Standard Shipping -- see Lists >> Accounting >> Shipping Items
        if (!empty($order->shipping_method['netsuite_id'])) {
            $so->shipMethod->internalId = $order->shipping_method['netsuite_id'];
        }
        /**
         * Set the shipping cost manually
         */
        $so->shippingCost = $order->shipping_total - $order->shipping_discount_total;
        
        $SelectCustomFieldRef = new \SelectCustomFieldRef();
        $SelectCustomFieldRef->internalId = 'custbody29'; // this is the "Sales Order Source" custom field
        $listOrRecordRef = new \ListOrRecordRef();
        $listOrRecordRef->internalId = '1'; // 1 = Tienda // TODO Rename this in the Amrita NS Interface
        $SelectCustomFieldRef->value = $listOrRecordRef;
        
        $StringCustomFieldRef = new \StringCustomFieldRef();
        $StringCustomFieldRef->internalId = 'custbody8';
        $StringCustomFieldRef->value = $order->comments; // "This is the custom 'Comments' field.  Customer comments go here.  This field may support longer text blobs.";
        
        $customFieldList = new \CustomFieldList();
        $customFieldList->customField = array( $StringCustomFieldRef, $SelectCustomFieldRef );
        $so->customFieldList = $customFieldList;

        $created = $order->created();
        $objDateTime = new \DateTime( $created['local'] );
        $so->startDate = $objDateTime->format(\DateTime::ISO8601);
        $so->endDate = $objDateTime->format(\DateTime::ISO8601);
        
        if (\Base::instance()->get('DEBUG')) {
            //$so->memo = "TEST - DO NOT FULFILL";
        }
        
        /**
         * Assign the customer to the sales order
         * using the $order->user_id
         */
        $user = (new \Users\Models\Users)->load(array('_id'=>new \MongoId( (string) $order->user_id)));
        
        if (empty($user->id)) 
        {
        	throw new \Exception( 'Invalid user for order' );
        }
        
        $customer_id = $user->{'netsuite.id'};
        if (empty($customer_id)) 
        {
            // do a search by email
            // TODO Change this so it searches a local cache of data, not a SOAP request
            if ($customerByEmail = \Netsuite\Models\Customer::fetchByEmail($user->email)) 
            {
                $customer_id = $customerByEmail->internalId;
                $user->set('netsuite.id', $customer_id);
                $user->save();
            }
            
            else {
                $customer_id = \Netsuite\Models\Customer::createFromUser($user);
            } 
        }
        
        $so->entity = new \RecordRef();
        $so->entity->internalId = $customer_id; // 19634; // this is the Netsuite internal id of the customer ("A Singh" = 19634) (97985 = Vika)
        $so->entity->type = \RecordType::customer;
        $so->entity->typeSpecified = true;
        
        $so->itemList = new \SalesOrderItemList();
        $so->itemList->item = array();
        foreach ($order->items as $orderitem)
        {
            $soi = new \SalesOrderItem();
            
            $price_level_id = null;
            
            $product_type = \Dsc\ArrayHelper::get( $orderitem, 'product.product_type' );
            switch ($product_type) 
            {
            	case "giftcard":
            	case "giftcards":
            	case "\\Shop\\Models\\GiftCards":
            	    $netsuite_id = 19338;
            	    $price_level_id = 6; // 1 = Wholesale, 6 = List Price, 5 = Current Web Price
            	    $record_type = \RecordType::nonInventorySaleItem;
            	    break;
            	default:
            	    $found_variant = array();
            	    foreach ((array) \Dsc\ArrayHelper::get( $orderitem, 'product.variants' ) as $variant)
            	    {
            	        if ($variant['id'] == (string) \Dsc\ArrayHelper::get( $orderitem, 'variant_id' ) ) {
            	            $found_variant = $variant;
            	        }
            	    }
            	    
            	    $netsuite_id = \Dsc\ArrayHelper::get( $found_variant, 'netsuite.id' );
            	    if (empty($found_variant) || !$netsuite_id)
            	    {
            	        continue 2;
            	    }

            	    // $price_level_id = 5; // this is "Current Web Price".  1 = Wholesale, 6 = List Price, 5 = Current Web Price            	    
            	    $price_level_id = 5;
            	    $record_type = \RecordType::inventoryItem;
            	    break;
            }
            
            $soi->item = new \RecordRef();
            $soi->item->internalId = $netsuite_id; // '14310'; // this is the internal id of the product/item, 14310 = ("ABC 01 - Pink")
            $soi->item->type = $record_type;
            $soi->quantity = (int) \Dsc\ArrayHelper::get( $orderitem, 'quantity' ); // 3;
            $soi->amount = (float) \Dsc\ArrayHelper::get( $orderitem, 'price' ) * $soi->quantity; // '55.3';
            if (!empty($price_level_id)) 
            {
                $soi->price = new \RecordRef();
                $soi->price->internalId = $price_level_id; // this is the internal id of the price level for this order-item
            }            
        
            $so->itemList->item[] = $soi;
        }
        
        // payment data
        if (!empty($order->payment_required)) 
        {
            $so->paymentMethod = new \RecordRef();
            $so->paymentMethod->internalId = 7; // Credit Card = 7, see response to PaymentMethodSearchBasic web services request
            
            $so->creditCard = new \RecordRef;
            $so->creditCard->internalId = \Dsc\ArrayHelper::get( $paymentData, 'card.token' );
            if ($csc = \Dsc\ArrayHelper::get( $paymentData, 'card.csc' )) 
            {
                $so->ccSecurityCode = $csc;
            }
            
            $so->creditCardProcessor = new \RecordRef();
            $so->creditCardProcessor->internalId = 2; // Cybersource = 2, Verisign = 1
            
            $so->getAuth = true;
            $so->saveOnAuthDecline = false;
            
            $orderBillingAddress = $order->billingAddress();
            if (!empty($orderBillingAddress->postal_code)
                && !empty($orderBillingAddress->line_1)
                && !empty($orderBillingAddress->name)
            )
            {
            	$so->ccName = $orderBillingAddress->name;
            	$so->ccStreet = $orderBillingAddress->line_1;
            	$so->ccZipCode = $orderBillingAddress->postal_code;
            }            

            /*
            $datetime = date( 'Y-m-01', strtotime( \Dsc\ArrayHelper::get( $paymentData, 'card.year' ) . '-' . \Dsc\ArrayHelper::get( $paymentData, 'card.month' ) . '-01' ) );
            $ccExpireDate = (new \DateTime( $datetime ))->format(\DateTime::ISO8601);
            $so->ccNumber = \Dsc\ArrayHelper::get( $paymentData, 'card.number' );
            $so->ccSecurityCode = \Dsc\ArrayHelper::get( $paymentData, 'card.cvv' );
            $so->ccExpireDate = $ccExpireDate;
            $so->ccName  = \Dsc\ArrayHelper::get( $order, 'shipping_address.name' );
            $so->creditCardProcessor = new \RecordRef();
            $so->creditCardProcessor->internalId = 2; // Cybersource = 2, Verisign = 1
            */
            
        }

        $so->shopperIpAddress = $_SERVER['REMOTE_ADDR'];
        
        // TAXES
        $so->isTaxable = true;
        
        if ($taxItem = static::taxItemFromAddress( $order->shippingAddress()->cast() )) 
        {
            // we're overriding the tax rate.  otherwise, netsuite will use the shipping address to determine the tax rate
            $so->taxItem = $taxItem;
            $taxRate = (\Dsc\ArrayHelper::get( $order, 'tax_total' ) / \Dsc\ArrayHelper::get( $order, 'sub_total' )) * 100; 
            $so->taxRate = $taxRate . '%';
        } 
        
        /**
         * Shipping address
         */
        if ($order->shippingAddress()) 
        {
            $shipAddress = new \RecordRef();
            $shipAddress->internalId = \Netsuite\Models\Address::fetchMatchOrCreate( $customer_id, $order->shippingAddress() );
            if (!$shipAddress->internalId)
            {
                throw new \Exception( 'Could not create shipping address record' );
            }
            $so->shipAddressList = $shipAddress;
        }
        
        /**
         * Billing address
         */
        $orderBillingAddress = $order->billingAddress();
        if (!empty($orderBillingAddress->country)
            && !empty($orderBillingAddress->region)
            && !empty($orderBillingAddress->postal_code)
            && !empty($orderBillingAddress->line_1)
            && !empty($orderBillingAddress->name)
            )
        {
            $billAddress = new \RecordRef();
            $billAddress->internalId = \Netsuite\Models\Address::fetchMatchOrCreate( $customer_id, $order->billingAddress() );
            if (!$billAddress->internalId)
            {
                throw new \Exception( 'Could not create billing address record' );
            }
            $so->billAddressList = $billAddress;
        }

        /**
         * Email address
         */        
        $so->email = $order->user_email;
        
        /**
         * Add the order's total discount as part of the Custom Website Discount item
         * See Lists > Website > Items, then change the Type == Discount
         */
        $so->discountItem = new \RecordRef();
        $so->discountItem->internalId = '19282'; // See "custom website discount" in Lists > Website > Items, then change the Type == Discount
        $so->discountItem->type = \RecordType::discountItem;        
        $so->discountRate = 0 - $order->discount_total + $order->shipping_discount_total - $order->giftcard_total - $order->credit_total;
        
        return $so;
    }
    
    public function submitViaRestlet( $data )
    {
        $url = 'https://rest.netsuite.com/app/site/hosting/restlet.nl?script=103&deploy=1';
        $data_string = json_encode($data);
        
        $headers = array(
            'Authorization: NLAuth nlauth_account=838520, nlauth_email=rdiaztushman@dioscouri.com, nlauth_signature=raf2013, nlauth_role=18',
            'Content-Type: application/json'
        );
    
        $this->_curl = curl_init();
        $opts = array(
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTPGET => false,
            CURLOPT_POST => true,
            CURLOPT_AUTOREFERER => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER => false,
            CURLOPT_NOBODY => false,
            CURLOPT_TIMEOUT => 0
        );
        
        curl_setopt_array( $this->_curl, $opts );
        curl_setopt( $this->_curl, CURLOPT_URL, $url );
        curl_setopt( $this->_curl, CURLOPT_SSL_VERIFYPEER, false );
        curl_setopt( $this->_curl, CURLINFO_HEADER_OUT, true ); // enable tracking
        curl_setopt( $this->_curl, CURLOPT_HTTPHEADER, $headers );
        curl_setopt( $this->_curl, CURLOPT_POSTFIELDS, $data_string);
        
        set_time_limit( 0 );
    
        $start = microtime( true );
        $response_raw = curl_exec( $this->_curl );
        $time_taken = microtime( true ) - $start;
    
        $errorMessage = "submitViaRestlet took " . number_format( $time_taken, 4 ) . " seconds";
        $this->log( $errorMessage, 'INFO', __NAMESPACE__ . __CLASS__ );
    
        $response = json_decode( $response_raw );
    
        return $response;
    }

    public static function fromShopOrder( \Shop\Models\Orders $order )
    {
        $service = \Netsuite\Factory::instance()->getService();
    
        //$order = $checkout->order();
        //$paymentData = $checkout->paymentData();
    
        $so = new \SalesOrder();
    
        $so->externalId = (string) $order->number; // 'RDT987654322'; // this is the tienda order id
        $so->department = new \RecordRef();
        $so->department->internalId = 1; // "Amrita Singh" dept -- see the Sales Order Form in the UI
        $so->class = new \RecordRef();
        $so->class->internalId = 3; // "Amrita Singh" class -- see the Sales Order Form in the UI
        $so->shipMethod = new \RecordRef();
        $so->shipMethod->internalId = 14119; // 14119 = Standard Shipping -- see Lists >> Accounting >> Shipping Items
        if (!empty($order->shipping_method['netsuite_id'])) {
            $so->shipMethod->internalId = $order->shipping_method['netsuite_id'];
        }
        /**
         * Set the shipping cost manually
         */
        $so->shippingCost = $order->shipping_total - $order->shipping_discount_total;
        
        /**
         * Add the order's total discount as part of the Custom Website Discount item
         * See Lists > Website > Items, then change the Type == Discount
         */
        $so->discountItem = new \RecordRef();
        $so->discountItem->internalId = '19282'; // See "custom website discount" in Lists > Website > Items, then change the Type == Discount
        $so->discountItem->type = \RecordType::discountItem;
        $so->discountRate = 0 - $order->discount_total + $order->shipping_discount_total - $order->giftcard_total - $order->credit_total;

        /**
         * If there is a positive shipping cost AND a positive store credit amount, 
         * then subtract as much of the shipping cost as possible from the store credit amount,
         * and reapply the new figures to the $so
         */
        if (($so->shippingCost > 0) && ($order->credit_total > 0)) 
        {
            if ($order->credit_total >= $so->shippingCost) 
            {
                $new_order_credit_total = $order->credit_total - $so->shippingCost;
                $so->shippingCost = 0;
                $so->discountRate = 0 - $order->discount_total + $order->shipping_discount_total - $order->giftcard_total - $new_order_credit_total;
            }
            else 
            {
                $so->shippingCost = $order->credit_total;
                $new_order_credit_total = 0;
                $so->discountRate = 0 - $order->discount_total + $order->shipping_discount_total - $order->giftcard_total - $new_order_credit_total;                
            }
        }
    
        $SelectCustomFieldRef = new \SelectCustomFieldRef();
        $SelectCustomFieldRef->internalId = 'custbody29'; // this is the "Sales Order Source" custom field
        $listOrRecordRef = new \ListOrRecordRef();
        $listOrRecordRef->internalId = '1'; // 1 = Tienda // TODO Rename this in the Amrita NS Interface
        $SelectCustomFieldRef->value = $listOrRecordRef;
    
        $StringCustomFieldRef = new \StringCustomFieldRef();
        $StringCustomFieldRef->internalId = 'custbody8';
        $StringCustomFieldRef->value = $order->comments; // "This is the custom 'Comments' field.  Customer comments go here.  This field may support longer text blobs.";
    
        $customFieldList = new \CustomFieldList();
        $customFieldList->customField = array( $StringCustomFieldRef, $SelectCustomFieldRef );
        $so->customFieldList = $customFieldList;
    
        $created = $order->created();
        $objDateTime = new \DateTime( $created['local'] );
        $so->startDate = $objDateTime->format(\DateTime::ISO8601);
        $so->endDate = $objDateTime->format(\DateTime::ISO8601);
    
        if (\Base::instance()->get('DEBUG')) {
            $so->memo = "TEST - DO NOT FULFILL";
        }
    
        /**
         * Assign the customer to the sales order
         * using the $order->user_id
         */
        $user = (new \Users\Models\Users)->load(array('_id'=>new \MongoId( (string) $order->user_id)));
    
        if (empty($user->id))
        {
            throw new \Exception( 'Invalid user for order' );
        }
    
        $customer_id = $user->{'netsuite.id'};
        if (empty($customer_id))
        {
            // do a search by email
            // TODO Change this so it searches a local cache of data, not a SOAP request
            if ($customerByEmail = \Netsuite\Models\Customer::fetchByEmail($user->email(true)))
            {
                $customer_id = $customerByEmail->internalId;
                $user->set('netsuite.id', $customer_id);
                $user->save();
            }
    
            else {
                $customer_id = \Netsuite\Models\Customer::createFromUser($user);
            }
        }
    
        $so->entity = new \RecordRef();
        $so->entity->internalId = $customer_id; // 19634; // this is the Netsuite internal id of the customer ("A Singh" = 19634) (97985 = Vika)
        $so->entity->type = \RecordType::customer;
        $so->entity->typeSpecified = true;
    
        $so->itemList = new \SalesOrderItemList();
        $so->itemList->item = array();
        foreach ($order->items as $orderitem)
        {
            $soi = new \SalesOrderItem();
    
            $price_level_id = null;
    
            $product_type = \Dsc\ArrayHelper::get( $orderitem, 'product.product_type' );
            switch ($product_type)
            {
                case "giftcard":
                case "giftcards":
                case "\\Shop\\Models\\GiftCards":
                    $netsuite_id = 19338;
                    $price_level_id = 6; // 1 = Wholesale, 6 = List Price, 5 = Current Web Price
                    $record_type = \RecordType::nonInventorySaleItem;
                    break;
                default:
                    $found_variant = array();
                    foreach ((array) \Dsc\ArrayHelper::get( $orderitem, 'product.variants' ) as $variant)
                    {
                        if ($variant['id'] == (string) \Dsc\ArrayHelper::get( $orderitem, 'variant_id' ) ) {
                            $found_variant = $variant;
                        }
                    }
                     
                    $netsuite_id = \Dsc\ArrayHelper::get( $found_variant, 'netsuite.id' );
                    if (empty($found_variant) || !$netsuite_id)
                    {
                        continue 2;
                    }
    
                    // $price_level_id = 5; // this is "Current Web Price".  1 = Wholesale, 6 = List Price, 5 = Current Web Price
                    $price_level_id = 5;
                    $record_type = \RecordType::inventoryItem;
                    break;
            }
    
            $soi->item = new \RecordRef();
            $soi->item->internalId = $netsuite_id; // '14310'; // this is the internal id of the product/item, 14310 = ("ABC 01 - Pink")
            $soi->item->type = $record_type;
            $soi->quantity = (int) \Dsc\ArrayHelper::get( $orderitem, 'quantity' ); // 3;
            $soi->amount = (float) \Dsc\ArrayHelper::get( $orderitem, 'price' ) * $soi->quantity; // '55.3';
            if (!empty($price_level_id))
            {
                $soi->price = new \RecordRef();
                $soi->price->internalId = $price_level_id; // this is the internal id of the price level for this order-item
            }
    
            $so->itemList->item[] = $soi;
        }
    
        // payment data
        switch ($order->payment_method_id) 
        {
            case "omnipay.paypal_express":
                // If this is a paypal transaction,
                // set payPalTranId (e.g. O-5EU43720JV4220317)
                // and set payPalStatus (e.g. Pending)
                // and set paypalAuthId (e.g. 813107789K704154X)
                // paypalProcess ?
                $so->paymentMethod = new \RecordRef();
                $so->paymentMethod->internalId = 9; // Paypal = 9, Credit Card = 7, see response to PaymentMethodSearchBasic web services request
                
                $so->getAuth = false;
                $so->ccApproved = true;
                
                $so->payPalTranId = $order->paymentMethodTranId();
                //$so->paypalAuthId = $order->paymentMethodAuthId(); // netsuite does not allow you to set it
                //$so->payPalStatus = $order->paymentMethodStatus(); // netsuite does not allow you to set it
                
                $so->pnRefNum = $order->paymentMethodTranId(); // ignored by netsuite
                $so->authCode = $order->paymentMethodAuthId(); // ignored by netsuite
                
                break;
            case "omnipay.cybersource":
                
                $payment_method_model = (new \Shop\Models\PaymentMethods)->setState('filter.identifier', $order->payment_method_id)->getItem(); 
                $payment_method = $payment_method_model->getClass();
                
                $ccAvsStreetMatch = $payment_method->avsStreetMatch( $order->payment_method_validation_result );
                if ($ccAvsStreetMatch === true) {
                    $so->ccAvsStreetMatch = \AvsMatchCode::_y;
                }
                elseif ($ccAvsStreetMatch === false) {
                    $so->ccAvsStreetMatch = \AvsMatchCode::_n;
                }
                elseif ($ccAvsStreetMatch === null) {
                    $so->ccAvsStreetMatch = \AvsMatchCode::_x;
                }
                
                $ccAvsZipMatch = $payment_method->avsZipMatch( $order->payment_method_validation_result );
                if ($ccAvsZipMatch === true) {
                    $so->ccAvsZipMatch = \AvsMatchCode::_y;
                }
                elseif ($ccAvsZipMatch === false) {
                    $so->ccAvsZipMatch = \AvsMatchCode::_n;
                }
                elseif ($ccAvsZipMatch === null) {
                    $so->ccAvsZipMatch = \AvsMatchCode::_x;
                }

                $ccSecurityCodeMatch = $payment_method->cvnMatch( $order->payment_method_validation_result );
                if ($ccSecurityCodeMatch === true) {
                    $so->ccSecurityCodeMatch = \AvsMatchCode::_y;
                }
                elseif ($ccSecurityCodeMatch === false) {
                    $so->ccSecurityCodeMatch = \AvsMatchCode::_n;
                }
                elseif ($ccSecurityCodeMatch === null) {
                    $so->ccSecurityCodeMatch = \AvsMatchCode::_x;
                }                
                
            default:
                // if this is a cybersource transaction
                $so->creditCardProcessor = new \RecordRef();
                $so->creditCardProcessor->internalId = 2; // Cybersource = 2, Verisign = 1
                
                // set ccApproved = 1
                // getAuth = 0
                // authCode (e.g. 281648)
                // pnRefNum (e.g. 4065818869300176056425)
                /*
                [ccAvsStreetMatch] =>
                [ccAvsZipMatch] =>
                [ccSecurityCodeMatch] => _y
                [altSalesTotal] =>
                [ignoreAvs] =>
                [paymentEventResult] => _accept
                [paymentEventHoldReason] =>
                [paymentEventType] => _authorizationRequest
                [paymentEventDate] => 2014-07-28T14:11:00.000-07:00
                [paymentEventUpdatedBy] => Rafael Diaz-Tushman
                */
                $so->paymentMethod = new \RecordRef();
                $so->paymentMethod->internalId = 7; // Credit Card = 7, see response to PaymentMethodSearchBasic web services request
                
                $so->getAuth = false;
                $so->ccApproved = true;
                
                $so->pnRefNum = $order->paymentMethodTranId();
                $so->authCode = $order->paymentMethodAuthId();
                                
                break;
        }
        
        $so->shopperIpAddress = $order->ip_address;
    
        // TAXES
        $so->isTaxable = true;
    
        if ($taxItem = static::taxItemFromAddress( $order->shippingAddress()->cast() ))
        {
            // we're overriding the tax rate.  otherwise, netsuite will use the shipping address to determine the tax rate
            $so->taxItem = $taxItem;
            $taxRate = (\Dsc\ArrayHelper::get( $order, 'tax_total' ) / \Dsc\ArrayHelper::get( $order, 'sub_total' )) * 100;
            $so->taxRate = $taxRate . '%';
        }
    
        /**
         * Shipping address
         */
        if ($order->shippingAddress())
        {
            $shipAddress = new \RecordRef();
            $shipAddress->internalId = \Netsuite\Models\Address::fetchMatchOrCreate( $customer_id, $order->shippingAddress() );
            if (!$shipAddress->internalId)
            {
                throw new \Exception( 'Could not create shipping address record' );
            }
            $so->shipAddressList = $shipAddress;
        }
    
        /**
         * Billing address
         */
        $orderBillingAddress = $order->billingAddress();
        if (!empty($orderBillingAddress->country)
            && !empty($orderBillingAddress->region)
            && !empty($orderBillingAddress->postal_code)
            && !empty($orderBillingAddress->line_1)
            && !empty($orderBillingAddress->name)
        )
        {
            $billAddress = new \RecordRef();
            $billAddress->internalId = \Netsuite\Models\Address::fetchMatchOrCreate( $customer_id, $order->billingAddress() );
            if (!$billAddress->internalId)
            {
                throw new \Exception( 'Could not create billing address record' );
            }
            $so->billAddressList = $billAddress;
        }
    
        /**
         * Email address
         */
        $so->email = $order->user_email;
        
        return $so;
    }    
}