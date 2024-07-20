<?php

use Yogaap\PHP\MVC\App\Router;
use Yogaap\PHP\MVC\Http\Controllers\HomeController;
use Yogaap\PHP\MVC\Http\Controllers\UserController;
use Yogaap\PHP\MVC\Middleware\AuthMiddleware;

require_once __DIR__ . '/../vendor/autoload.php';

Router::add('GET', '/', HomeController::class, 'index');

Router::group(['middlewares' => [AuthMiddleware::class]], function () {
    Router::group(['prefix' => 'users', 'controller' => UserController::class], function () {
        Router::add('GET', '/login', 'loginPage');
        Router::add('POST', '/login', 'login');

        Router::add('GET', '/register', 'registerPage');
        Router::add('POST', '/register', 'register');
    
        Router::add('GET', '/profile', 'profilePage');
        Router::add('POST', '/profile/{id}', 'profile');

        Router::add('GET', '/password', 'passwordPage');
        Router::add('POST', '/password/{id}', 'password');

        Router::add('GET', '/logout', 'logout');
    });

    Router::group(['prefix' => 'home', 'controller' => HomeController::class], function () {
        Router::add('GET', '/dashboard', 'dashboard');
    });
});

Router::run();