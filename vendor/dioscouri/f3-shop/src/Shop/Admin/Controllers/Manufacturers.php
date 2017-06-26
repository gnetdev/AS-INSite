<?php 
namespace Shop\Admin\Controllers;

class Manufacturers extends \Admin\Controllers\BaseAuth 
{
    use \Dsc\Traits\Controllers\AdminList;
    
    protected $list_route = '/admin/shop/manufacturers';

    protected function getModel()
    {
        $model = new \Shop\Models\Manufacturers;
        return $model;
    }
    
    public function index()
    {
        $model = $this->getModel();
        
        $state = $model->emptyState()->populateState()->getState();
        \Base::instance()->set('state', $state );
        
        $paginated = $model->paginate();
        \Base::instance()->set('paginated', $paginated );
        
        \Base::instance()->set('selected', 'null' );
        
        $this->app->set('meta.title', 'Manufacturers | Shop');
        
        $view = \Dsc\System::instance()->get('theme');
        echo $view->render('Shop/Admin/Views::manufacturers/list.php');
    }
    
    public function getDatatable()
    {
        $model = $this->getModel();
        
        $state = $model->populateState()->getState();
        \Base::instance()->set('state', $state );
        
        $paginated = $model->paginate();
        \Base::instance()->set('paginated', $paginated );
    
        $view = \Dsc\System::instance()->get('theme');
        $html = $view->renderLayout('Shop/Admin/Views::manufacturers/list_datatable.php');
        
        return $this->outputJson( $this->getJsonResponse( array(
                'result' => $html
        ) ) );
    
    }
    
    public function getAll()
    {
        $model = $this->getModel();
        $manufacturers = $model->getList();
        \Base::instance()->set('manufacturers', $manufacturers );

        \Base::instance()->set('selected', 'null' );
        
        $view = \Dsc\System::instance()->get('theme');
        $html = $view->renderLayout('Shop/Admin/Views::manufacturers/list_parents.php');
        
        return $this->outputJson( $this->getJsonResponse( array(
                'result' => $html
        ) ) );
    
    }
    
    public function getCheckboxes()
    {
        $model = $this->getModel();
        $manufacturers = $model->getList();
        \Base::instance()->set('manufacturers', $manufacturers );
    
        $selected = array();
        $data = \Base::instance()->get('REQUEST');
        
        $input = $data['category_ids'];
        foreach ($input as $id) 
        {
            $id = $this->inputfilter->clean( $id, 'alnum' );
            $selected[] = array('id' => $id);
        }

        $flash = \Dsc\Flash::instance();
        $flash->store( array( 'metadata'=>array('manufacturers'=>$selected) ) );
        \Base::instance()->set('flash', $flash );
        
        $view = \Dsc\System::instance()->get('theme');
        $html = $view->renderLayout('Shop/Admin/Views::manufacturers/checkboxes.php');
    
        return $this->outputJson( $this->getJsonResponse( array(
                'result' => $html
        ) ) );
    
    }
    
    public function selectList( $selected=null )
    {
        $model = $this->getModel();
        $manufacturers = $model->getList();
        \Base::instance()->set('manufacturers', $manufacturers );
        \Base::instance()->set('selected', $selected );
         
        $view = \Dsc\System::instance()->get('theme');
        echo $view->renderLayout('Shop/Admin/Views::manufacturers/list_parents.php');
    }
}