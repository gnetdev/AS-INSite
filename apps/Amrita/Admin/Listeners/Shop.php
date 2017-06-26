<?php 
namespace Amrita\Admin\Listeners;

class Shop extends \Prefab 
{
    public function onDisplayShopProductsEdit( $event )
    {
        $item = $event->getArgument('item');
        $tabs = $event->getArgument('tabs');
        $content = $event->getArgument('content');
        
        $tabs[] = 'Amrita Singh';
        $content[] = \Amrita\Admin\Controllers\ShopProduct::instance()->edit($event);
        
        $event->setArgument('tabs', $tabs);
        $event->setArgument('content', $content);
    }
    
    public function onDisplayShopCollectionsEdit( $event )
    {
        $item = $event->getArgument('item');
        $tabs = $event->getArgument('tabs');
        $content = $event->getArgument('content');
    
        $tabs[] = 'Amrita Singh';
        $content[] = \Amrita\Admin\Controllers\ShopCollection::instance()->edit($event);
    
        $event->setArgument('tabs', $tabs);
        $event->setArgument('content', $content);
    }
}