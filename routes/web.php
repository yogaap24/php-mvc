<?php

use Yogaap\PHP\MVC\App\Router;
use Yogaap\PHP\MVC\Http\Controllers\HomeController;
use Yogaap\PHP\MVC\Http\Controllers\UserController;
use Yogaap\PHP\MVC\Middleware\AuthMiddleware;
use Yogaap\PHP\MVC\Middleware\CSRFMiddleware;

Router::add('GET', '/', HomeController::class, 'index');

Router::group(['middlewares' => [AuthMiddleware::class]], function () {
    Router::group(['prefix' => 'users', 'controller' => UserController::class], function () {
        Router::add('GET', '/login', 'loginPage');
        Router::add('POST', '/login', 'login', '', [CSRFMiddleware::class]);

        Router::add('GET', '/register', 'registerPage');
        Router::add('POST', '/register', 'register', '', [CSRFMiddleware::class]);

        Router::add('GET', '/profile', 'profilePage');
        Router::add('POST', '/profile/{id}', 'profile', '', [CSRFMiddleware::class]);

        Router::add('GET', '/password', 'passwordPage');
        Router::add('POST', '/password/{id}', 'password', '', [CSRFMiddleware::class]);

        Router::add('GET', '/logout', 'logout');
    });

    Router::group(['prefix' => 'home', 'controller' => HomeController::class], function () {
        Router::add('GET', '/dashboard', 'dashboard');
    });
});