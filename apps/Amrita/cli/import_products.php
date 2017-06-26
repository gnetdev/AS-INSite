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

require $app->get('PATH_ROOT') . 'config/config.php';

// add a route
$app->route('GET /', '\Amrita\Models\Import\Products->run');

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

$app->run();