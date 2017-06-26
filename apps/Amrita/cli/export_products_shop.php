<?php
define('PATH_ROOT', realpath( __dir__ . '/../../../' ) . '/' );

$app = require( PATH_ROOT . 'vendor/bcosca/fatfree/lib/base.php');

$app->set('PATH_ROOT', PATH_ROOT);
$app->set('AUTOLOAD',
        $app->get('PATH_ROOT') . 'lib/;' .
        $app->get('PATH_ROOT') . 'apps/;'
);

require $app->get('PATH_ROOT') . 'vendor/autoload.php';

$app->set('APP_NAME', 'cli');

set_error_handler(
    function($code,$text) use($app) {
        if (error_reporting()) {
        	switch ($code) {
        		case E_DEPRECATED:
        		    echo "deprecation error";
        		    $app->set('HALT', false);
        		    $app->error(500,$text);
        		    break;
        		default:
        		    $app->error(500,$text);
        		    break;
        	}
        }
    }
);

require $app->get('PATH_ROOT') . 'config/config.php';

// add a route
$app->route('GET /', '\Amrita\Models\Export\ShopProducts->run');

// bootstap each mini-app
\Dsc\Apps::instance()->bootstrap();

// trigger the preflight event
\Dsc\System::instance()->preflight();

$db_name = \Base::instance()->get('db.mongo.database');
$db_server = \Base::instance()->get('db.mongo.server');
if ($db_name && $db_server) {
    \Dsc\System::instance()->container->share( 'mongo', function() use ($db_server, $db_name) {
        return new \MongoDB( new \MongoClient($db_server), $db_name);
        // see this bug: https://jira.mongodb.org/browse/PHP-928
        // when it's resolved and part of the current PECL Mongo package, revert this.
        //return new \DB\Mongo($db_server, $db_name);
    } );
}

// Login as the safemode user
$safemode_enabled = \Base::instance()->get('safemode.enabled');
$safemode_user = \Base::instance()->get('safemode.username');
$safemode_email = \Base::instance()->get('safemode.email');
$safemode_password = \Base::instance()->get('safemode.password');
$safemode_id = \Base::instance()->get('safemode.id');

$regex = '/^[0-9a-z]{24}$/';
if (preg_match($regex, (string) $safemode_id))
{
    $safemode_id = new \MongoId($safemode_id);
}
else
{
    $safemode_id = new \MongoId();
}

$user = new \Users\Models\Users;
$user->id = $safemode_id;
$user->username = $safemode_user;
$user->first_name = $safemode_user;
$user->password = $safemode_password;
$user->email = $safemode_email;
$role = \Base::instance()->get('safemode.role');
if (!$role) {
    $role = 'root';
}
$user->role = $role;
$user->__safemode = true;
\Users\Lib\Auth::instance()->setIdentity( $user );

// run the app
$app->run();