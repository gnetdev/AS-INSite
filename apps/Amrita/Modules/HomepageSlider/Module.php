<?php 
namespace Amrita\Modules\HomepageSlider;

class Module extends \Modules\Abstracts\Module
{
    public function html()
    {
        \Base::instance()->set('module', $this);
        
        \Dsc\System::instance()->get('theme')->registerViewPath( __dir__ . '/Views/', 'Amrita/Modules/HomepageSlider/Views' );
        $string = \Dsc\System::instance()->get('theme')->renderLayout('Amrita/Modules/HomepageSlider/Views::default.php');
    
        return $string;
    }
}
