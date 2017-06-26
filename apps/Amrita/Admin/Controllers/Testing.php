<?php
namespace Amrita\Admin\Controllers;

class Testing extends \Admin\Controllers\BaseAuth
{

    public function toSalesOrder()
    {
        $id = $this->input->get('id');
        $message = null;
        
        try
        {
            if (empty($id))
            {
                throw new \Exception('Please specify an order ID to push to Netsuite');
            }
            
            $order = (new \Shop\Models\Orders())->setState('filter.id', $id)->getItem();
            if (empty(($order->id)))
            {
                throw new \Exception('Invalid order ID');
            }
            
            $so = \Amrita\Models\SalesOrder::fromShopOrder($order);
            $message = \Dsc\Debug::dump($so);
        }
        
        catch (\Exception $e)
        {
            $message = $e->getMessage();
            \Dsc\System::addMessage($message, 'error');
        }
        
        $this->app->set('message', $message);
        echo $this->theme->render('SystemTheme/Views::message.php');
    }

    public function abandonedCartEmail()
    {
        $message = null;
        
        $cart_id = '53ecc05bf02e254d7911cca5';
        $notification_idx = '1';
        
        $settings = \Shop\Models\Settings::fetch();
        $subject = $settings->get('abandoned_cart_subject');
        $cart = (new \Shop\Models\CartsAbandoned())->setState('filter.id', $cart_id)->getItem();
        
        // cart was deleted so dont do anything
        if (empty($cart->id))
        {
            return;
        }
        
        // Has the cart been updated recently? if so, don't send this email
        $abandoned_time = $settings->get('abandoned_cart_time') * 60;
        $abandoned_time = time() - $abandoned_time;
        if ($cart->{'metadata.last_modified.time'} > $abandoned_time)
        {
            return;
        }
        
        $user = (new \Users\Models\Users())->setState('filter.id', $cart->user_id)->getItem();
        
        // get correct user email
        $recipients = array();
        if (empty($cart->{'user_email'}))
        {
            $recipients = array(
                $user->email
            );
        }
        else
        {
            $recipients = array(
                $cart->{'user_email'}
            );
        }
        
        $token = \Dsc\System::instance()->get('auth')->getAutoLoginToken($user, true);
        
        \Base::instance()->set('cart', $cart);
        \Base::instance()->set('user', $user);
        \Base::instance()->set('idx', $notification_idx);
        \Base::instance()->set('token', $token);
        
        $notification = $settings->get('abandoned_cart_emails.' . $notification_idx);
        if (empty($notification))
        {
            $notification = array(
                'text' => array(
                    'html' => '',
                    'plain' => ''
                )
            );
        }
        \Base::instance()->set('notification', $notification);
        
        $f3 = \Base::instance();
        \Dsc\System::instance()->get('theme')->setTheme('Theme', $f3->get('PATH_ROOT') . 'apps/Theme/');
        \Dsc\System::instance()->get('theme')->registerViewPath($f3->get('PATH_ROOT') . 'apps/Theme/Views/', 'Theme/Views');
        
        $html = \Dsc\System::instance()->get('theme')->renderView('Shop/Views::emails_html/abandoned_cart.php');
        $text = \Dsc\System::instance()->get('theme')->renderView('Shop/Views::emails_text/abandoned_cart.php');
        
        $message = $html;
        
        \Dsc\System::instance()->get('theme')->setTheme('AdminTheme');
        
        $this->app->set('message', $message);
        echo $this->theme->render('SystemTheme/Views::message.php');
    }

    public function abandonedCarts()
    {
        $message = null;
        
        try
        {
            $items = (new \Shop\Models\CartsAbandoned())->setState('filter.abandoned', '1')
                ->setState('filter.abandoned_only_new', '1')
                ->getList();
            
            foreach ($items as $item)
            {
                $email = $item->user_email;
                if (empty($item->user_email))
                {
                    $email = $item->user()->email;
                }
                $message .= "<div class='alert alert-info'>";
                $message .= 'user_id: ' . $item->user_id . '<br/>';
                $message .= 'email: ' . $email . '<br/>';
                $message .= 'items count: ' . $item->quantity() . '<br/>';
                $message .= 'value: ' . $item->total() . '<br/>';
                $message .= "</div>";
            }
            
            \Dsc\System::addMessage('abandonedCarts', 'success');
        }
        
        catch (\Exception $e)
        {
            \Dsc\System::addMessage($e->getMessage(), 'error');
        }
        
        $this->app->set('message', $message);
        echo $this->theme->render('SystemTheme/Views::message.php');
    }

    public function getCurrencies()
    {
        $settings = \Shop\Models\Settings::fetch();
        if (empty($settings->{'currency.openexchangerates_api_id'}))
        {
            $this->app->set('message', 'Please provide an open exchange rates API ID in the Shop Configuration page');
            echo $this->theme->render('SystemTheme/Views::message.php');
            return;
        }
        
        $oer = new \Shop\Lib\OpenExchangeRates($settings->{'currency.openexchangerates_api_id'});
        if ($response = $oer->currencies())
        {
            if ($currencies = \Joomla\Utilities\ArrayHelper::fromObject($response))
            {
                foreach ($currencies as $code => $title)
                {
                    $currency = (new \Shop\Models\Currencies())->setParam('conditions', array(
                        'code' => $code
                    ))->getItem();
                    
                    if (empty($currency->id))
                    {
                        $currency = new \Shop\Models\Currencies();
                    }
                    
                    $currency->code = $code;
                    $currency->title = $title;
                    $currency->store();
                }
            }
        }
        
        $message = \Dsc\Debug::dump($currencies);
        
        $this->app->set('message', $message);
        echo $this->theme->render('SystemTheme/Views::message.php');
    }

    public function fetchRefundFigures()
    {
        $model = new \Amrita\Models\Import\RefundFigures();
        $model->doFetchAndSave();
        
        $model = new \Amrita\Models\Export\RefundFigures();
        $model->doFetchAndSave();        
        
        $message = 'Check the System logs for results';
        
        /* 
        $message = \Dsc\Debug::dump($results);
        
        if (!empty($results->results))
        {
            foreach ($results->results as $result)
            {
                $row = new \Amrita\Models\Import\RefundFigures();
                $row->load(array(
                    'ns_id' => $result->id,
                    'line_id' => $result->line_id
                ));
                
                foreach ($result as $key => $value)
                {
                    switch ($key)
                    {
                        case "id":
                            $row->set('ns_id', $value);
                            break;
                        default:
                            if (!empty($key) && !is_null($value))
                            {
                                $row->set($key, $value);
                            }
                            break;
                    }
                }
                
                $row->processed = false;
                $row->save();
            }
        }
        */
        
        $this->app->set('message', $message);
        echo $this->theme->render('SystemTheme/Views::message.php');
    }

    public function exportRefundFigures()
    {
        $message = null;
        
        $model = new \Amrita\Models\Import\RefundFigures();
        $conditions = $model->conditions();
        /*
         * $conditions['processed'] = array( '$in' => array('', null) );
         */
        $conditions['limit'] = 5;
        
        if ($results = $model->find($conditions))
        {
            // $message = \Dsc\Debug::dump($results);
            
            foreach ($results as $result)
            {
                // $message = null; // TODO Use this for logging when in model
                $message .= '<p class="alert alert-info">Processing: ' . $result->ns_id . ', line: ' . $result->{'line_id.value'} . '</p>';
                
                if (!empty($result->{'processed.time'}))
                {
                    $message .= \Dsc\Debug::dump('Skipping item because it has already been processed');
                    continue;
                }
                
                if ($result->{'Status.value'} != 'refunded')
                {
                    $message .= \Dsc\Debug::dump('Skipping item because status is not refunded');
                    continue;
                }
                
                if (!empty($result->{'Netsuite Sales Order Number.value'}))
                {
                    $sales_order_id = $result->{'Netsuite Sales Order Number.value'};
                }
                
                elseif (!empty($result->{'associated CC/Cash Sale.value'}))
                {
                    // $cash_sale = \Netsuite\Models\CashSale::fetchById($result->{'associated CC/Cash Sale.value'});
                    // $sales_order_id = $cash_sale->{'createdFrom.internalId'};
                    
                    // $sales_order_id = $cash_sale->{createdFrom.internalId} == the sales order's internal ID
                    // $message .= \Dsc\Debug::dump($cash_sale);
                    
                    $message .= \Dsc\Debug::dump('No sales order ID');
                }
                
                if (!empty($sales_order_id))
                {
                    // is there a tienda order for this sales order?
                    $order = \Shop\Models\Orders::findOne(array(
                        'netsuite.id' => $sales_order_id
                    ));
                    
                    if (empty($order->id))
                    {
                        $message .= \Dsc\Debug::dump('No Shop Order found');
                        
                        $amount = 0 + $result->{'associated Refund amount.value'};
                        
                        $message .= \Dsc\Debug::dump('Amount: ' . $amount);
                    }
                    
                    else
                    {
                        $message .= \Dsc\Debug::dump('Order ID: ' . $order->id);
                        
                        // did the order originate in the online shop?
                        if ($order->{'source.id'} == 'shop')
                        {
                            // has this adjustment already been done?
                            // use the ns_id (the RA's internal ID)
                            // and the line_id (line_id.value)
                            // as a two-column index
                            
                            $ns_id = $result->ns_id;
                            $line_id = $result->{'line_id.value'};
                            
                            $adjustment = $order->{'amrita.adjustments.' . $ns_id . '.' . $line_id};
                            if (!$adjustment)
                            {
                                $amount = 0 + $result->{'associated Refund amount.value'};
                                $date = $result->{'Refund Last Modified Date.value'};
                                $display_title = 'Adjustment';
                                
                                // do the adjustment
                                $adjustment = array(
                                    'ns_id' => $ns_id,
                                    'line_id' => $line_id,
                                    'display_title' => $display_title,
                                    'amount' => $amount,
                                    'date' => $date
                                );
                                
                                $order->{'amrita.adjustments.' . $ns_id . '.' . $line_id} = $adjustment;
                                
                                $order->grand_total = $order->grand_total + $amount;
                                $order->adjustments_total = $order->adjustments_total + $amount;
                                
                                // add it to the history
                                $order->history[] = array(
                                    'created' => \Dsc\Mongo\Metastamp::getDate('now'),
                                    'verb' => 'adjusted',
                                    'details' => $adjustment
                                );
                                
                                // save it
                                $order->store();
                            }
                            else
                            {
                                // TODO update the adjustment?
                            }
                        }
                    }
                }
                
                $result->processed = \Dsc\Mongo\Metastamp::getDate('now');
                $result->processing_result = $message;
                $result->store();
            }
        }
        
        $this->app->set('message', $message);
        echo $this->theme->render('SystemTheme/Views::message.php');
    }
    
    public function sendBirthdayToMailchimp()
    {
        $id = $this->input->get('id');
        $message = null;
        
        try
        {
            if (empty($id))
            {
                throw new \Exception('Please specify a User ID');
            }
        
            \Amrita\Models\Customers::sendBirthdayToMailchimp( $id );
        }
        
        catch (\Exception $e)
        {
            $message = $e->getMessage();
            \Dsc\System::addMessage($message, 'error');
        }
        
        $this->app->set('message', $message);
        echo $this->theme->render('SystemTheme/Views::message.php');        
    }
    
    public function requestProductReview()
    {
        $message = null;
        $order_id = $this->input->get('id');
        
        if ($order_id) {
            \Shop\Models\ProductReviews::sendEmailForOrder( $order_id );
        } else {
            \Dsc\System::addMessage('Missing an Order ID', 'error');
        }
        
        $this->app->set('message', $message);
        echo $this->theme->render('SystemTheme/Views::message.php');        
    }
    
    public function fixImportedNSOrders()
    {
        $messages = array();
        
        foreach (\Shop\Models\Orders::collection()->find(array('items.variant_id'=>null)) as $doc) 
        {
            $order = new \Shop\Models\Orders($doc);
            $updated = false;
            
            $messages[] = "processing order " . $order->id;
            
            foreach ($order->items as $key=>&$item)
            {
                if (empty($item['product_id']) || empty($item['variant_id']))
                {
                    if (empty($item['product_id'])) 
                    {
                        $messages[] = "product_id is missing";
                    }
                    
                    if (empty($item['variant_id'])) 
                    {
                        $messages[] = "variant_id is missing";
                    }
                    
                    $netsuite_id = \Dsc\ArrayHelper::get($item, 'product.netsuite.id');
                    $messages[] = \Dsc\Debug::dump($item);
                    
                    $p = (new \Shop\Models\Products)->load( array(
                        'variants.netsuite.id' => $netsuite_id
                    ) );
                    
                    if (!empty($p->id)) 
                    {
                        $messages[] = $p->title;
                        $messages[] = "is the product found for netsuite.id: " . $netsuite_id;
                        
                        $item['product_id'] = $p->id;
                        $updated = true;
                        
                        $variant = (new \Netsuite\Models\Export\ShopProducts)->findVariant( $netsuite_id, $p );
                        if (!empty($variant['id'])) 
                        {
                            $messages[] = "variant found for netsuite.id";
                            $item['variant_id'] = $variant['id'];
                        }
                        elseif (empty($item['variant_id'])) 
                        {
                            $item['variant_id'] = null;
                            $messages[] = "NO variant_id found for netsuite.id";
                        }
                    }
                    else 
                    {
                        $messages[] = "NO product found for netsuite.id";
                        $item['product_id'] = null;
                        $item['variant_id'] = null;
                        $updated = true;                        
                    }
                }
            }
            
            if ($updated) 
            {
                $order->store();
            }
        }
        
        $message = implode('<br/>', $messages);
        $this->app->set('message', $message);
        echo $this->theme->render('SystemTheme/Views::message.php');        
    }
    
    public function fixAllWishlists()
    {
        foreach (\Shop\Models\Wishlists::collection()->find() as $doc)
        {
            set_time_limit( 0 );
    
            $item = new \Shop\Models\Wishlists( $doc );
            $item->items_count = count($item->items);
            $item->store();
        }
    
        echo $this->theme->render('SystemTheme/Views::message.php');
    }
}