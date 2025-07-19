<?php

use Core\Http\Router;

// Set API configuration
Router::api('v1');

// Public API routes
Router::group(['prefix' => 'auth'], function() {
    Router::post('/login', 'Modules\User\Controller\UserController@login');
    Router::post('/register', 'Modules\User\Controller\UserController@register');
});

// Protected API routes
Router::group(['middleware' => ['Core\Middleware\AuthMiddleware']], function() {
    // User API routes
    Router::group(['prefix' => 'users'], function() {
        Router::get('/profile', 'Modules\User\Controller\UserController@profile');
        Router::put('/profile', 'Modules\User\Controller\UserController@profile');
        Router::post('/password', 'Modules\User\Controller\UserController@password');
        Router::post('/logout', 'Modules\User\Controller\UserController@logout');
    });

    // Home API routes
    Router::group(['prefix' => 'home'], function() {
        Router::get('/dashboard', 'Modules\Home\Controller\HomeController@dashboard');
    });

    // Resource routes example
    // Router::resource('posts', 'Modules\Post\Controller\PostController');
});
