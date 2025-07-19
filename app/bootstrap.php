<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Core\Foundation\Application;
use Core\Http\Router;
use App\Config\Environment;
use App\Providers\CoreServiceProvider;
use App\Providers\DatabaseServiceProvider;

// Load environment
Environment::load();

// Create application
$app = new Application();

// Register service providers
$app->registerProvider(new CoreServiceProvider($app->getContainer()));
$app->registerProvider(new DatabaseServiceProvider($app->getContainer()));

// Set container for static Router
Router::setContainer($app->getContainer());

return $app;