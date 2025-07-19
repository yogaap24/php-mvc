<?php

namespace App\Providers;

use Core\Foundation\Container;
use Core\Middleware\AuthMiddleware;
use Core\Middleware\CorsMiddleware;
use Core\Middleware\CSRFMiddleware;
use Core\Service\SessionService;
use Core\Service\ResponseService;
use Core\Service\ValidationService;
use Core\View\View;

class CoreServiceProvider
{
    public function register(Container $container): void
    {
        // Register session service
        $container->singleton('session', function() {
            return SessionService::class;
        });

        // Register response service
        $container->singleton('response', function() {
            return ResponseService::class;
        });

        // Register validation service
        $container->singleton('validation', function() {
            return ValidationService::class;
        });

        // Register view service
        $container->singleton('view', function() {
            return View::class;
        });

        // Register core middleware
        $container->bind('middleware.auth', AuthMiddleware::class);
        $container->bind('middleware.csrf', CSRFMiddleware::class);
        $container->bind('middleware.cors', CorsMiddleware::class);
    }

    public function boot(): void
    {
        // Initialize session
        SessionService::start();
    }
}