<?php

use Yogaap\PHP\MVC\App\Router;
use Yogaap\PHP\MVC\Controller\HomeController;
use Yogaap\PHP\MVC\Controller\ProductController;
use Yogaap\PHP\MVC\Middleware\AuthMiddleware;

require_once __DIR__ . '/../vendor/autoload.php';

Router::add('GET', '/', HomeController::class, 'index');
Router::add('GET', '/login', HomeController::class, 'login');
Router::add('GET', '/about', HomeController::class, 'about', [AuthMiddleware::class]);
Router::add('GET', '/contact', HomeController::class, 'contact', [AuthMiddleware::class]);

Router::add('GET', '/products/{id}/categories/{category}', ProductController::class, 'categories');

Router::run();