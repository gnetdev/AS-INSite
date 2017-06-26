<?php
namespace Amrita\Admin;

class Routes extends \Dsc\Routes\Group
{

    public function initialize()
    {
        $this->setDefaults(array(
            'namespace' => '\Amrita\Admin\Controllers',
            'url_prefix' => '/admin/amrita'
        ));

        $this->add('/site/home', 'GET|POST', array(
            'controller' => 'Settings',
            'action' => 'siteHome'
        ));
        
        $this->add('/shop/home', 'GET|POST', array(
            'controller' => 'Settings',
            'action' => 'shopHome'
        ));
        
        $this->add('/mailchimp', 'GET|POST', array(
            'controller' => 'Mailchimp',
            'action' => 'index'
        ));
        
        $this->add('/customer/sync/@id', 'GET', array(
            'controller' => 'Customers',
            'action' => 'sync'
        ));        
        
        $this->add('/customer/downloadOrderHistorySince/@id', 'GET', array(
            'controller' => 'Customers',
            'action' => 'downloadOrderHistorySince'
        ));

        $this->app->route( 'GET /admin/amrita/testing/@task', '\Amrita\Admin\Controllers\Testing->@task' );
        $this->app->route( 'GET /admin/amrita/testing/@task/page/@page', '\Amrita\Admin\Controllers\Testing->@task' );

        $this->add('/refundfigures', 'GET|POST', array(
            'controller' => 'RefundFigures',
            'action' => 'index'
        ));
        
        $this->add('/refundfigures/page/@page', 'GET', array(
            'controller' => 'RefundFigures',
            'action' => 'index'
        ));        

        $this->add('/refundfigures/view/@id', 'GET', array(
            'controller' => 'RefundFigures',
            'action' => 'view'
        ));

        $this->add('/refundfigures/export/@id', 'GET', array(
            'controller' => 'RefundFigures',
            'action' => 'export'
        ));
        
        $this->add('/refundfigures/fetch-and-save', 'GET', array(
            'controller' => 'RefundFigures',
            'action' => 'fetchAndSave'
        ));
    }
}