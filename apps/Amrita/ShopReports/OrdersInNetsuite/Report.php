<?php 
namespace Amrita\ShopReports\OrdersInNetsuite;

class Report extends \Shop\Abstracts\Report 
{
    public function bootstrap()
    {
        $this->theme->registerViewPath( __dir__ . '/Views/', 'Amrita/ShopReports/OrdersInNetsuite/Views' );
        
        // Register any custom routes that the report needs
        $this->app->route( 'GET /admin/shop/reports/'.$this->slug().'/pushOrder/@id', '\\' . __CLASS__ . '->pushOrder' );
        
        return parent::bootstrap();
    }
    
    /**
     * Primary entry-point for the report.
     * Supports GET & POST
     */
    public function index()
    {
        $model = (new \Amrita\Models\ShopOrders)->emptyState()->populateState();
        
        try {
            $paginated = $model->paginate();
        } catch ( \Exception $e ) {
            \Dsc\System::addMessage( $e->getMessage(), 'error');
            $this->app->reroute( '/admin/shop/reports/' . $this->slug() );
            return;
        }
        
        $this->app->set('state', $model->getState());
        $this->app->set('paginated', $paginated);
        
        echo $this->theme->render('Amrita/ShopReports/OrdersInNetsuite/Views::index.php');
    }
    
    public function pushOrder()
    {
        $id = $this->app->get('PARAMS.id');
    
        try
        {
            if (empty($id))
            {
                throw new \Exception( 'Please specify an order ID to push to Netsuite' );
            }
    
            $netsuite_order_id = \Amrita\Models\ShopOrders::pushToNetsuite( $id );
            if (!empty($netsuite_order_id)) {
                \Dsc\System::addMessage( 'Created in Netsuite as Sales Order #' . $netsuite_order_id , 'success' );
            } else {
                throw new \Exception( 'There was an error pushing to Netsuite.  Check the System > Logs for more details.' );
            }
        }
    
        catch (\Exception $e)
        {
            \Dsc\System::addMessage( $e->getMessage(), 'error' );
        }
    
        $this->app->reroute( '/admin/shop/reports/' . $this->slug() );
    }    
}