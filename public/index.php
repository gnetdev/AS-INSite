<?php
$app = require '../vendor/bcosca/fatfree/lib/base.php';

$app->set('PATH_ROOT', __dir__ . '/../');
$app->set('AUTOLOAD', $app->get('PATH_ROOT') . 'lib/;' . $app->get('PATH_ROOT') . 'apps/;');

require $app->get('PATH_ROOT') . 'vendor/autoload.php';

$app->set('APP_NAME', 'site');
if (strpos(strtolower($app->get('URI')), $app->get('BASE') . '/admin') !== false)
{
    $app->set('APP_NAME', 'admin');
}

require $app->get('PATH_ROOT') . 'config/config.php';

// bootstap each mini-app
\Dsc\Apps::instance()->bootstrap();

// load routes
\Dsc\System::instance()->get('router')->registerRoutes();

// trigger the preflight event
\Dsc\System::instance()->preflight();

//\Cache::instance()->reset();

$app->run();