<?php
namespace AmritaIndia\Admin;

class Routes extends \Dsc\Routes\Group
{

    public function initialize()
    {
        $this->setDefaults(array(
            'namespace' => '\AmritaIndia\Admin\Controllers',
            'url_prefix' => '/admin/amrita-india'
        ));

        $this->add('/shipping', 'GET|POST', array(
            'controller' => 'Shipping',
            'action' => 'index'
        ));
    }
}