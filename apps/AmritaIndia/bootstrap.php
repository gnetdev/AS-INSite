<?php
class AmritaIndiaBootstrap extends \Dsc\Bootstrap
{
    protected $dir = __DIR__;
    protected $base = __DIR__;
    protected $namespace = 'AmritaIndia';
    
    /**
     * Register this app's view files for all global_apps
     * @param string $global_app
     */
    protected function registerViewFiles($global_app)
    {
        \Dsc\System::instance()->get('theme')->registerViewPath($this->dir . '/' . $global_app . '/Views/', $this->namespace . '/' . $global_app . '/Views');
    }
    
    /**
     * Triggered when the admin global_app is run
     */
    protected function runAdmin()
    {
        parent::runAdmin();
    }
    
    /**
     * Triggered when the front-end global_app is run
     */
    protected function runSite()
    {
        parent::runSite();
    }
    
}

$f3 = \Base::instance();
switch (strtolower($f3->get('HOST')))
{
    case "119.9.95.28":
    case "amritasingh.co.in":
    case "dev.amritasingh.co.in":
        
        $app = new AmritaIndiaBootstrap();
        
        break;
    case "dev.dioscouri.com":
    case "dev.amritasingh.com":
    case "banglebangle.web2":
    case "banglebangle.com":
    case "amritasingh.com":
    default:
        
        break;
}
