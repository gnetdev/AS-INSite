<?php
namespace Amrita\Admin;

class Listener extends \Prefab
{

    public function onSystemRebuildMenu($event)
    {
        if ($model = $event->getArgument('model'))
        {
            $root = $event->getArgument('root');
            $amrita = clone $model;
            
            $amrita->insert(array(
                'type' => 'admin.nav',
                'priority' => 10,
                'title' => 'Amrita',
                'icon' => 'fa fa-female',
                'is_root' => false,
                'tree' => $root,
                'base' => '/admin/amrita'
            ));
            
            $children = array(
                array(
                    'title' => 'Homepage',
                    'route' => './admin/amrita/site/home',
                    'icon' => 'fa fa-home'
                ),
                array(
                    'title' => 'Shop Homepage',
                    'route' => './admin/amrita/shop/home',
                    'icon' => 'fa fa-shopping-cart'
                ),
                array(
                    'title' => 'Mailchimp',
                    'route' => './admin/amrita/mailchimp',
                    'icon' => 'fa fa-envelope'
                ),
                array(
                    'title' => 'Refund Figures',
                    'route' => './admin/amrita/refundfigures',
                    'icon' => 'fa fa-list'
                ),                
            );
            $amrita->addChildren($children, $root);
            
            \Dsc\System::instance()->addMessage('Amrita added its admin menu items.');
        }
    }
    
    public function onDisplayShopCustomers($event)
    {
        $item = $event->getArgument('item');
        $tabs = $event->getArgument('tabs');
        $content = $event->getArgument('content');
    
        \Base::instance()->set('item', $event->getArgument('item'));
        $view = \Dsc\System::instance()->get('theme');
        $html = $view->renderLayout('Amrita/Admin/Views::shop_customer/read.php');
    
        $tabs[] = 'Amrita';
        $content[] = $html;
    
        $event->setArgument('tabs', $tabs);
        $event->setArgument('content', $content);
    }    
}