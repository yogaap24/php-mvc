<?php

use Core\Http\Router;

// Public routes
Router::get('/', 'Modules\Home\Controller\HomeController@index');

// User authentication routes (no auth middleware needed for login/register)
Router::group(['prefix' => 'users'], function() {
    Router::get('/login', 'Modules\User\Controller\UserController@loginPage');
    Router::post('/login', 'Modules\User\Controller\UserController@login');

    Router::get('/register', 'Modules\User\Controller\UserController@registerPage');
    Router::post('/register', 'Modules\User\Controller\UserController@register');

    Router::get('/logout', 'Modules\User\Controller\UserController@logout');
});

// Protected routes
Router::group(['middleware' => ['Core\Middleware\AuthMiddleware']], function() {
    // User profile routes
    Router::group(['prefix' => 'users'], function() {
        Router::get('/profile', 'Modules\User\Controller\UserController@profilePage');
        Router::post('/profile', 'Modules\User\Controller\UserController@profile');

        Router::get('/password', 'Modules\User\Controller\UserController@passwordPage');
        Router::post('/password', 'Modules\User\Controller\UserController@password');
    });

    // Dashboard routes
    Router::group(['prefix' => 'home'], function() {
        Router::get('/dashboard', 'Modules\Home\Controller\HomeController@dashboard');
    });
});