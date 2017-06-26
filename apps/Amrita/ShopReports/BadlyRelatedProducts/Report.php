<?php 
namespace Amrita\ShopReports\BadlyRelatedProducts;

class Report extends \Shop\Abstracts\Report 
{
    public function bootstrap()
    {
        $this->theme->registerViewPath( __dir__ . '/Views/', 'Amrita/ShopReports/BadlyRelatedProducts/Views' );
        
        // Register any custom routes that the report needs
        $this->app->route( 'GET /admin/shop/reports/'.$this->slug().'/populate', '\\' . __CLASS__ . '->populate' );
        $this->app->route( 'GET /admin/shop/reports/'.$this->slug().'/disassociate/@id', '\\' . __CLASS__ . '->disassociate' );
        $this->app->route( 'GET /admin/shop/reports/'.$this->slug().'/disassociate-all', '\\' . __CLASS__ . '->disassociateAll' );
        
        $this->app->route( 'GET /admin/shop/reports/'.$this->slug().'/posts', '\\' . __CLASS__ . '->indexPosts' );
        $this->app->route( 'GET /admin/shop/reports/'.$this->slug().'/posts/populate', '\\' . __CLASS__ . '->populatePosts' );
        $this->app->route( 'GET /admin/shop/reports/'.$this->slug().'/posts/disassociate/@id', '\\' . __CLASS__ . '->disassociatePosts' );
        $this->app->route( 'GET /admin/shop/reports/'.$this->slug().'/posts/disassociate-all', '\\' . __CLASS__ . '->disassociateAllPosts' );        
        
        return parent::bootstrap();
    }
    
    /**
     * Primary entry-point for the report.
     * Supports GET & POST
     */
    public function index()
    {
        $model = new \Amrita\ShopReports\BadlyRelatedProducts\Models\Pages;
        
        try {
            $paginated = $model->paginate();
        } catch ( \Exception $e ) {
            \Dsc\System::addMessage( $e->getMessage(), 'error');
            $this->app->reroute( '/admin/shop/reports/' . $this->slug() );
            return;
        }
        
        $this->app->set('state', $model->getState());
        $this->app->set('paginated', $paginated);
        
        echo $this->theme->render('Amrita/ShopReports/BadlyRelatedProducts/Views::pages.php');
    }
    
    public function populate()
    {
        try
        {
            $model = new \Amrita\ShopReports\BadlyRelatedProducts\Models\Pages;
            $model->populate();
            \Dsc\System::addMessage( 'Report data refreshed' );
        }
    
        catch (\Exception $e)
        {
            \Dsc\System::addMessage( $e->getMessage(), 'error' );
        }
    
        $this->app->reroute( '/admin/shop/reports/' . $this->slug() );
    }

    public function disassociate()
    {
        try
        {
            $model = (new \Amrita\ShopReports\BadlyRelatedProducts\Models\Pages)->setState('filter.id', $this->app->get('PARAMS.id'))->getItem()->disassociate();
            \Dsc\System::addMessage( 'Items disassociated', 'success' );
        }
    
        catch (\Exception $e)
        {
            \Dsc\System::addMessage( $e->getMessage(), 'error' );
        }
    
        $this->app->reroute( '/admin/shop/reports/' . $this->slug() );
    }
    
    public function disassociateAll()
    {
        try
        {
            \Amrita\ShopReports\BadlyRelatedProducts\Models\Pages::disassociateAll();
            \Dsc\System::addMessage( 'Items disassociated', 'success' );
        }
    
        catch (\Exception $e)
        {
            \Dsc\System::addMessage( $e->getMessage(), 'error' );
        }
    
        $this->app->reroute( '/admin/shop/reports/' . $this->slug() . '/populate' );
    }
    
    /**
     * Primary entry-point for the report.
     * Supports GET & POST
     */
    public function indexPosts()
    {
        $model = new \Amrita\ShopReports\BadlyRelatedProducts\Models\Posts;
    
        try {
            $paginated = $model->paginate();
        } catch ( \Exception $e ) {
            \Dsc\System::addMessage( $e->getMessage(), 'error');
            $this->app->reroute( '/admin/shop/reports/' . $this->slug() );
            return;
        }
    
        $this->app->set('report', $this->report());
        $this->app->set('state', $model->getState());
        $this->app->set('paginated', $paginated);
        
        $this->app->set('meta.title', $this->report()->title . ' | Reports | Shop');
    
        echo $this->theme->render('Amrita/ShopReports/BadlyRelatedProducts/Views::posts.php');
    }
    
    public function populatePosts()
    {
        try
        {
            $model = new \Amrita\ShopReports\BadlyRelatedProducts\Models\Posts;
            $model->populate();
            \Dsc\System::addMessage( 'Report data refreshed' );
        }
    
        catch (\Exception $e)
        {
            \Dsc\System::addMessage( $e->getMessage(), 'error' );
        }
    
        $this->app->reroute( '/admin/shop/reports/' . $this->slug() . '/posts' );
    }
    
    public function disassociatePosts()
    {
        try
        {
            $model = (new \Amrita\ShopReports\BadlyRelatedProducts\Models\Posts)->setState('filter.id', $this->app->get('PARAMS.id'))->getItem()->disassociate();
            \Dsc\System::addMessage( 'Items disassociated', 'success' );
        }
    
        catch (\Exception $e)
        {
            \Dsc\System::addMessage( $e->getMessage(), 'error' );
        }
    
        $this->app->reroute( '/admin/shop/reports/' . $this->slug() . '/posts' );
    }
    
    public function disassociateAllPosts()
    {
        try
        {
            \Amrita\ShopReports\BadlyRelatedProducts\Models\Posts::disassociateAll();
            \Dsc\System::addMessage( 'Items disassociated', 'success' );
        }
    
        catch (\Exception $e)
        {
            \Dsc\System::addMessage( $e->getMessage(), 'error' );
        }
    
        $this->app->reroute( '/admin/shop/reports/' . $this->slug() . '/posts/populate' );
    }
}