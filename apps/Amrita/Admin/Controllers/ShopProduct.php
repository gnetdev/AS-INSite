<?php 
namespace Amrita\Admin\Controllers;

class ShopProduct extends \Admin\Controllers\BaseAuth 
{
	public function edit($event)
	{
	    \Base::instance()->set('item', $event->getArgument('item'));
	    
		$view = \Dsc\System::instance()->get('theme');
		return $view->renderLayout('Amrita/Admin/Views::shopproduct/edit.php');
	}
}