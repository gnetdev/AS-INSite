<?php 
namespace Amrita\Admin\Controllers;

class Mailchimp extends Settings 
{
	public function index()
	{
	    $this->app->set('meta.title', 'Mailchimp Settings');
		
		$this->settings_route = '/admin/amrita/mailchimp';
		
		switch ($this->app->get('VERB')) {
			case "POST":
			case "post":
			    // do the save and redirect to $this->settings_route
			    return $this->save();
			    break;
		}
		
		$flash = \Dsc\Flash::instance();
		$this->app->set('flash', $flash );
		
		$settings = \Amrita\Models\Settings::fetch();
		$flash->store( $settings->cast() );
		
		$view = \Dsc\System::instance()->get('theme');
		echo $this->theme->renderTheme('Amrita/Admin/Views::mailchimp/index.php');
	}
}