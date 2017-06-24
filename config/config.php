<?php
ini_set('session.cookie_lifetime', 365*24*60*60);
ini_set('session.cookie_httponly', false);
$app->config($app->get('PATH_ROOT') . 'config/common.config.ini');

switch (strtolower($app->get('HOST')))
{
    case "119.9.95.28":
    case "amritasingh.co.in":
    case "amritasingh.co.in2":
        $app->config($app->get('PATH_ROOT') . 'config/india.live.config.ini');
        break;    
    case "dev.amritasingh.co.in":
        $app->config($app->get('PATH_ROOT') . 'config/india.dev.config.ini');
        break;
    case "dev.dioscouri.com":
    case "dev.amritasingh.com":
        $app->config($app->get('PATH_ROOT') . 'config/dev.config.ini');
        break;
    case "banglebangle.web2":
    case "banglebangle.com":
    case "amritasingh.com":
    default:
        $app->config($app->get('PATH_ROOT') . 'config/live.config.ini');
        break;
}

$app->set('LOGS', realpath( $app->get('PATH_ROOT') . 'logs/' ) . '/' );
$app->set('TEMP', realpath( $app->get('PATH_ROOT') . 'tmp/' ) . '/' );
$app->set('db.jig.dir', realpath( $app->get('PATH_ROOT') . 'jig/' ) . '/' );

if ($app->get('DEBUG'))
{
    ini_set('display_errors', 1);
    if (!$app->get('CACHE'))
    {
        \Cache::instance()->reset();
    }
}

$logger = new \Log($app->get('application.logfile'));
\Registry::set('logger', $logger);
