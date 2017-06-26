<?php 
namespace Shop\Admin\Controllers;

class Manufacturer extends \Admin\Controllers\BaseAuth 
{
    use \Dsc\Traits\Controllers\CrudItemCollection;

    protected $list_route = '/admin/shop/manufacturers';
    protected $create_item_route = '/admin/shop/manufacturer/create';
    protected $get_item_route = '/admin/shop/manufacturer/read/{id}';    
    protected $edit_item_route = '/admin/shop/manufacturer/edit/{id}';
    
    protected function getModel() 
    {
        $model = new \Shop\Models\Manufacturers;
        return $model; 
    }
    
    protected function getItem() 
    {
        $f3 = \Base::instance();
        $id = $this->inputfilter->clean( $f3->get('PARAMS.id'), 'alnum' );
        $model = $this->getModel()
            ->setState('filter.id', $id);

        try {
            $item = $model->getItem();
        } catch ( \Exception $e ) {
            \Dsc\System::instance()->addMessage( "Invalid Item: " . $e->getMessage(), 'error');
            $f3->reroute( $this->list_route );
            return;
        }

        return $item;
    }
    
    protected function displayCreate() 
    {
        $f3 = \Base::instance();

        $model = $this->getModel();
        $all = $model->emptyState()->getList();
        \Base::instance()->set('all', $all );
        
        \Base::instance()->set('selected', null );
        
        $this->app->set('meta.title', 'Create Manufacturer | Shop');
        
        $view = \Dsc\System::instance()->get('theme');
        $view->event = $view->trigger( 'onDisplayShopManufacturersEdit', array( 'item' => $this->getItem(), 'tabs' => array(), 'content' => array() ) );
        echo $view->render('Shop/Admin/Views::manufacturers/create.php');        
    }
    
    protected function displayEdit()
    {
        $f3 = \Base::instance();

        $model = $this->getModel();
        $manufacturers = $model->emptyState()->getList();
        \Base::instance()->set('manufacturers', $manufacturers );
        
        $flash = \Dsc\Flash::instance();
        $selected = $flash->old('parent');
        \Base::instance()->set('selected', $selected );
        
        $this->app->set('meta.title', 'Edit Manufacturer | Shop');
        
        $view = \Dsc\System::instance()->get('theme');
        $view->event = $view->trigger( 'onDisplayShopManufacturersEdit', array( 'item' => $this->getItem(), 'tabs' => array(), 'content' => array() ) );
        echo $view->render('Shop/Admin/Views::manufacturers/edit.php');
    }
    
    /**
     * This controller doesn't allow reading, only editing, so redirect to the edit method
     */
    protected function doRead(array $data, $key=null) 
    {
        $f3 = \Base::instance();
        $id = $this->getItem()->get( $this->getItemKey() );
        $route = str_replace('{id}', $id, $this->edit_item_route );
        $f3->reroute( $route );
    }
    
    protected function displayRead() {}
    
    public function quickadd()
    {
    	$model = $this->getModel();
    	$manufacturers = $model->getList();
    	\Base::instance()->set('manufacturers', $manufacturers );
    	 
    	$view = \Dsc\System::instance()->get('theme');
    	echo $view->renderLayout('Shop/Admin/Views::manufacturers/quickadd.php');
    }
}