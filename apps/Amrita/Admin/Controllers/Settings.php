<?php 
namespace Amrita\Admin\Controllers;

class Settings extends \Admin\Controllers\BaseAuth 
{
	use \Dsc\Traits\Controllers\Settings;
	
	protected $layout_link = 'Amrita/Admin/Views::settings/default.php';
	protected $settings_route = '/admin/amrita/settings';
    
    protected function getModel()
    {
        $model = new \Amrita\Models\Settings;
        return $model;
    }
    
    public function siteHome()
    {
        $this->settings_route = '/admin/amrita/site/home';
    
        $f3 = \Base::instance();
        switch ($f3->get('VERB')) {
        	case "POST":
        	case "post":
        	    // do the save and redirect to $this->settings_route
        	    return $this->save();
        	    break;
        }
    
        $flash = \Dsc\Flash::instance();
        $f3->set('flash', $flash );
    
        $settings = \Amrita\Models\Settings::fetch();
        $flash->store( $settings->cast() );
        
        $this->app->set('meta.title', 'Homepage | Amrita');
    
        $view = \Dsc\System::instance()->get('theme');
        echo $view->renderTheme('Amrita/Admin/Views::settings/site_home.php');
    }
    
    public function shopHome()
    {
        $this->settings_route = '/admin/amrita/shop/home';
        
        $f3 = \Base::instance();
        switch ($f3->get('VERB')) {
        	case "POST":
        	case "post":
        	    // do the save and redirect to $this->settings_route 
        	    return $this->save();
        	    break;
        }

        $flash = \Dsc\Flash::instance();
        $f3->set('flash', $flash );
        
        $settings = \Amrita\Models\Settings::fetch();
        $flash->store( $settings->cast() );        
        
        $this->app->set('meta.title', 'Shop Home | Amrita');
        
        $view = \Dsc\System::instance()->get('theme');
        echo $view->renderTheme('Amrita/Admin/Views::settings/shop_home.php');
    }
}