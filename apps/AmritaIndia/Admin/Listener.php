<?php
namespace AmritaIndia\Admin;

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
                'title' => 'Amrita - India',
                'icon' => 'fa fa-female',
                'is_root' => false,
                'tree' => $root,
                'base' => '/admin/amrita-india'
            ));
            
            $children = array(
                array(
                    'title' => 'Shipping',
                    'route' => './admin/amrita-india/shipping',
                    'icon' => 'fa fa-truck'
                ),
            );
            $amrita->addChildren($children, $root);
            
            \Dsc\System::instance()->addMessage('Amrita - India - added its admin menu items.');
        }
    }
}