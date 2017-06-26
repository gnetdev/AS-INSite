<?php
class AmritaBootstrap extends \Dsc\Bootstrap
{
    protected $dir = __DIR__;
    protected $base = __DIR__;
    protected $namespace = 'Amrita';
    
    /**
     * Register this app's view files for all global_apps
     * @param string $global_app
     */
    protected function registerViewFiles($global_app)
    {
        \Dsc\System::instance()->get('theme')->registerViewPath($this->dir . '/' . $global_app . '/Views/', $this->namespace . '/' . $global_app . '/Views');
    }
    
    /**
     * Triggered when the admin global_app is run
     */
    protected function runAdmin()
    {
        parent::runAdmin();
        
        $f3 = \Base::instance();
    
        // register the template'e module positions
        \Modules\Factory::registerPositions( array(
            'homepage1-promo1', 'homepage1-promo2', 
            'homepage1-shipping', 'homepage1-featured1', 'homepage1-featured2', 'homepage1-featured3',
            'homepage2-slider',
            'homepage2-shipping', 'homepage2-featured1', 'homepage2-featured2', 'homepage2-featured3',                    
        ) );

        // register the modules path
        \Modules\Factory::registerPath( $f3->get('PATH_ROOT') . "apps/Amrita/Modules/" );
        
        // register event listeners
        \Dsc\System::instance()->getDispatcher()->addListener(\Amrita\Admin\Listeners\Shop::instance());
        \Dsc\System::instance()->getDispatcher()->addListener(\Amrita\Site\Listeners\Shop::instance());
        
        // register shipping methods
        \Shop\Models\ShippingMethods::register( array(
            (new \Shop\Models\ShippingMethods( array(
                'id' => 'amrita.domestic.standard',
                'name' => 'Continental U.S. - Standard'
            ) ) )->cast(),
            (new \Shop\Models\ShippingMethods( array(
                'id' => 'amrita.domestic.2_day',
                'name' => 'Continental U.S. - 2nd Day',
            ) ) )->cast(),
            (new \Shop\Models\ShippingMethods( array(
                'id' => 'amrita.domestic.1_day',
                'name' => 'Continental U.S. - Overnight',
            ) ) )->cast(),
            (new \Shop\Models\ShippingMethods( array(
                'id' => 'amrita.domestic_non-continental.standard',
                'name' => 'Non-Continental U.S. - Standard',
            ) ) )->cast(),
            (new \Shop\Models\ShippingMethods( array(
                'id' => 'amrita.international.standard',
                'name' => 'International - Standard',
            ) ) )->cast(),
            (new \Shop\Models\ShippingMethods( array(
            'id' => 'amrita.international.express',
            'name' => 'International - Express',
            ) ) )->cast()
        ) );        
        
        // register reports
        \Shop\Models\Reports::register('\Amrita\ShopReports\OrdersInNetsuite', array(
            'title'=>'Orders - in Netsuite',
            'icon'=>'fa fa-inbox',
            'type'=>'amrita',
            'slug'=>'orders-in-netsuite'
        ));

        // register reports
        \Shop\Models\Reports::register('\Amrita\ShopReports\BadlyRelatedProducts', array(
            'title'=>'Badly Related Products',
            'icon'=>'fa fa-ambulance',
            'type'=>'amrita',
            'slug'=>'badly-related-products'
        ));
    }
    
    /**
     * Triggered when the front-end global_app is run
     */
    protected function runSite()
    {
        parent::runSite();
        
        \Dsc\System::instance()->getDispatcher()->addListener(\Amrita\Site\Listeners\Shop::instance());
        \Dsc\System::instance()->getDispatcher()->addListener(\Amrita\Site\Listeners\Users::instance());

        // add the css & js files to the minifier
        \Minify\Factory::registerPath( $this->dir . "/" );
        
        $files = array();
        $files[] = '../Amrita/Assets/js/jquery.serializejson.js';
        $files[] = '../Amrita/Assets/js/jquery.tabSlideOut.js';
        
        /* Moved to bottom of theme/index.php
        if ($sync_customer = \Dsc\System::instance()->get('session')->get('amrita.sync.customer')) 
        {
            //$files[] = '../Amrita/Assets/js/sync_customer.js';
        }
        */
        
        foreach ( $files as $file )
        {
            \Minify\Factory::js( $file );
        }        
    }
    
}
$app = new AmritaBootstrap();