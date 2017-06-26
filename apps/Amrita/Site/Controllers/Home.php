<?php 
namespace Amrita\Site\Controllers;

class Home extends \Dsc\Controller 
{
    public function display()
    {
        $settings = \Amrita\Models\Settings::fetch();
        $this->app->set('settings', $settings);
        
        $title = $settings->{'site_home.page_title'} ? $settings->{'site_home.page_title'} : 'Designer Indian Jewelry and Fashion Accessories';
        $this->app->set('meta.title', $title);
        
        $desc = $settings->{'site_home.page_description'};
        $this->app->set('meta.description', $desc);
        
        echo $this->theme->render('Amrita\Site\Views::home/default.php');
    }
    
    public function version()
    {
        $settings = \Amrita\Models\Settings::fetch();
        $this->app->set('settings', $settings);
        
        $title = $settings->{'site_home.page_title'} ? $settings->{'site_home.page_title'} : 'Designer Indian Jewelry and Fashion Accessories';
        $this->app->set('meta.title', $title);
        
        $desc = $settings->{'site_home.page_description'};
        $this->app->set('meta.description', $desc);        
        
        $version_number = (int) \Base::instance()->get('PARAMS.version_number');
        
        // If the file doesn't exist, just use the default
        if (!$this->theme->findViewFile( 'Amrita\Site\Views::home/version_' . $version_number . '.php' )) 
        {
            echo $this->theme->render('Amrita\Site\Views::home/default.php');
            return;
        }
        
        echo $this->theme->render('Amrita\Site\Views::home/version_' . $version_number . '.php');
    }
}