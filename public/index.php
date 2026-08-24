<?php

require __DIR__ . '/../vendor/autoload.php';

use Bramus\Router\Router;
use JoonWeb\Context;

// Initialize SDK Context
$config = require __DIR__ . '/../config/joonweb.php';
Context::init([
    'api_key' => $config['api_key'],
    'api_secret' => $config['api_secret'],
    'api_version' => $config['api_version'],
    'app_name' => $config['app_name']
]);

$router = new Router();

// Setup Routes
$router->get('/auth/install', '\App\Controllers\AuthController@install');
$router->get('/auth/callback', '\App\Controllers\AuthController@callback');
$router->post('/webhooks', '\App\Controllers\WebhookController@handle');

$router->get('/', '\App\Controllers\DashboardController@index');

// Run Router
$router->run();
