<?php 
namespace Amrita\Modules\SoftLaunch;

class Module extends \Modules\Abstracts\Module
{
    public function html()
    {
        \Base::instance()->set('module', $this);
        
        \Dsc\System::instance()->get('theme')->registerViewPath( __dir__ . '/Views/', 'Amrita/Modules/SoftLaunch/Views' );
        $string = \Dsc\System::instance()->get('theme')->renderLayout('Amrita/Modules/SoftLaunch/Views::index.php');
    
        return $string;
    }
}
