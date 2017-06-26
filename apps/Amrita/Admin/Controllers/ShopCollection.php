<?php 
namespace Amrita\Admin\Controllers;

class ShopCollection extends \Admin\Controllers\BaseAuth 
{
	public function edit($event)
	{
	    \Base::instance()->set('item', $event->getArgument('item'));
	    
		$view = \Dsc\System::instance()->get('theme');
		return $view->renderLayout('Amrita/Admin/Views::shopcollection/edit.php');
	}
}