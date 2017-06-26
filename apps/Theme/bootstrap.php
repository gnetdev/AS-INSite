<?php
class ThemeBootstrap extends \Dsc\Bootstrap
{
    protected $dir = __DIR__;
    protected $base = __DIR__;
    protected $namespace = 'Theme';
    
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

        $f3 = \Base::instance();
        
        // Tell the admin that this is an available front-end theme
        \Dsc\System::instance()->get('theme')->registerTheme('Theme', $f3->get('PATH_ROOT') . 'apps/Theme/' );
        
        // register this theme's module positions with the admin
        \Modules\Factory::registerPositions( array('theme-head', 'theme-below-header', 'theme-above-footer', 'theme-below-footer', 'theme-above-content', 'theme-below-content', 'theme-left-content', 'theme-right-content', 'product-finalsale') );
    }
    
    /**
     * Triggered when the cli global_app is run
     */
    protected function runCli()
    {
        parent::runCli();
    
        $f3 = \Base::instance();
    
        // Tell the system that this is an available front-end theme with available overrides
        \Dsc\System::instance()->get('theme')->registerTheme('Theme', $f3->get('PATH_ROOT') . 'apps/Theme/' );
        \Dsc\System::instance()->get('theme')->setTheme('Theme', $f3->get('PATH_ROOT') . 'apps/Theme/' );
    }    

    /**
     * Triggered when the front-end global_app is run
     */
    protected function runSite()
    {
        parent::runSite();

        $f3 = \Base::instance();

        \Dsc\System::instance()->get('theme')->setTheme('Theme', $f3->get('PATH_ROOT') . 'apps/Theme/' );
        \Dsc\System::instance()->get('theme')->registerViewPath( $f3->get('PATH_ROOT') . 'apps/Theme/Views/', 'Theme/Views' );

        // tell Minify where to find Media, CSS and JS files
        \Minify\Factory::registerPath($f3->get('PATH_ROOT') . "public/theme/vendor/owl-carousel/");
        \Minify\Factory::registerPath($f3->get('PATH_ROOT') . "public/theme/");
        \Minify\Factory::registerPath($f3->get('PATH_ROOT') . "public/");
        
        // register the less css file
        \Minify\Factory::registerLessCssSource( $f3->get('PATH_ROOT') . "apps/Theme/Less/global.less.css" );

        // add the media assets to be minified
        $files = array(
            'dsc/css/common.css',
            //'css/font-awesome.min.css',
            //'css/bootstrap.min.css',
            //'css/flexslider.css',
            //'css/chosen.css',
            'css/slider.css',
            'css/style.css',
            //'rmm/css/customized.css',
            'rmm/css/mega-menu-responsive.css',
            'rmm/css/custom.css',
            'vendor/jqzoom/jqzoom.css',
            'vendor/imagezoom/imagezoom.css',
            'vendor/owl-carousel/owl.carousel.css',
        );

        foreach ($files as $file)
        {
            \Minify\Factory::css($file);
        }

        $files = array(
            //'js/vendor/modernizr-2.6.2-respond-1.1.0.min.js',
            //'js/vendor/jquery-1.10.1.min.js',
            //'js/vendor/jquery.flexslider-min.js',
            //'js/vendor/jquery.jcarousel.min.js',
            //'js/vendor/jquery.placeholder.min.js',
            'js/vendor/tinynav.min.js',
            'js/vendor/jquery.raty.min.js',
            //'js/vendor/chosen.jquery.min.js',
            'js/vendor/bootstrap-slider.js',
            //'js/vendor/bootstrap.min.js',
            //'site/js/bootstrap-hover-dropdown.js',
            'vendor/jqzoom/jqzoom.js',
            'vendor/imagezoom/imagezoom.min.js',
            'js/custom/remove_broken_images.js',
            'js/custom/responsive_menu.js',
            'vendor/owl-carousel/owl.carousel.min.js',
            //'bootbox/bootbox.js',
            'dsc/js/common.js',
            'js/main.js',
            'js/custom.js',
        );

        foreach ($files as $file)
        {
            \Minify\Factory::js($file, array('priority' => 1));
        }
        
        \Dsc\System::instance()->getDispatcher()->addListener(\Theme\Listeners\Error::instance());
        
        // symlink to the public folder if necessary
        if (!is_dir($f3->get('PATH_ROOT') . 'public/ThemeAssets'))
        {
            $public_theme = $f3->get('PATH_ROOT') . 'public/ThemeAssets';
            $theme_assets = realpath(__dir__ . '/Assets');
            $res = symlink($theme_assets, $public_theme);
        }
    }
}
$app = new ThemeBootstrap();