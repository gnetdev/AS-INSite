<?php
namespace Amrita\Site;

class Routes extends \Dsc\Routes\Group
{

    public function initialize()
    {
        $this->setDefaults(array(
            'namespace' => '\Amrita\Site\Controllers',
            'url_prefix' => ''
        ));

        $this->add('/', 'GET', array(
            'controller' => 'Home',
            'action' => 'display'
        ));
        
        $this->add('/home/version/@version_number', 'GET', array(
            'controller' => 'Home',
            'action' => 'version'
        ));
        
        $this->add('/amrita/customer/sync', 'GET', array(
            'controller' => 'Customer',
            'action' => 'sync'
        ));
        
        $this->add('/amrita/customer/sync/force', 'GET', array(
            'controller' => 'Customer',
            'action' => 'forceSync'
        ));
        
        $this->add('/support/case', 'GET', array(
            'controller' => 'Support',
            'action' => 'create'
        ));
        
        $this->add('/support/case', 'POST', array(
            'controller' => 'Support',
            'action' => 'createSubmit'
        ));
        
        $this->add('/support/case/created', 'GET', array(
            'controller' => 'Support',
            'action' => 'createConfirmation'
        ));
        
        $this->add('/user/credit-cards', 'GET', array(
            'controller' => 'CreditCards',
            'action' => 'index'
        ));
        
        $this->add('/user/credit-cards/delete/@id', 'GET', array(
            'controller' => 'CreditCards',
            'action' => 'remove'
        ));        
        
    }
}