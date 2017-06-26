<?php 
namespace Shop\Admin\Controllers;

class Customer extends \Users\Admin\Controllers\User 
{
    use \Dsc\Traits\Controllers\CrudItemCollection;

    protected $list_route = '/admin/shop/customers';
    protected $create_item_route = '/admin/shop/customer/create';
    protected $get_item_route = '/admin/shop/customer/read/{id}';
    protected $edit_item_route = '/admin/shop/customer/edit/{id}';
    
    protected function getModel($name='Users')
    {
        switch (strtolower($name))
        {
            case "user":
            case "users":
        	case "customer":
        	case "customers":
        	    $model = new \Shop\Models\Customers;
        	    break;
        	default:
        	    $model = parent::getModel($name);
        	    break;
        }
    
        return $model;
    }    
    
    protected function displayCreate() 
    {
        $this->app->set('meta.title', 'Create Customer | Shop');
        
        $this->theme->event = $this->theme->trigger( 'onDisplayShopCustomersEdit', array( 'item' => $this->getItem(), 'tabs' => array(), 'content' => array() ) );
        echo $this->theme->render('Shop/Admin/Views::customers/create.php');        
    }
    
    protected function displayEdit()
    {
        $this->app->set('meta.title', 'Edit Customer | Shop');
        
        $this->theme->event = $this->theme->trigger( 'onDisplayShopCustomersEdit', array( 'item' => $this->getItem(), 'tabs' => array(), 'content' => array() ) );        
        echo $this->theme->render('Shop/Admin/Views::customers/edit.php');
    }
    
    protected function displayRead() 
    {
        $this->app->set('meta.title', 'Customer | Shop');
        
        $this->theme->event = $this->theme->trigger( 'onDisplayShopCustomers', array( 'item' => $this->getItem(), 'tabs' => array(), 'content' => array() ) );
        echo $this->theme->render('Shop/Admin/Views::customers/read.php');    	
    }
    
    public function refreshTotals()
    {
        $customer = $this->getItem();
        
        if (empty($customer->id)) 
        {
            \Dsc\System::addMessage('Invalid ID', 'error');
            $this->app->reroute('/admin/shop/customers');
        }
        
        $customer->{'shop.total_spent'} = $customer->totalSpent(true);
        $customer->{'shop.orders_count'} = $customer->ordersCount(true);
        
        try 
        {
            $customer->save();
            $customer->checkCampaigns();
            
            \Dsc\System::addMessage('Totals refreshed', 'success');
        }
        
        catch (\Exception $e) 
        {
            \Dsc\System::addMessage($e->getMessage(), 'error');
        }
        
        $this->app->reroute('/admin/shop/customer/read/' . $customer->id);
    }
}