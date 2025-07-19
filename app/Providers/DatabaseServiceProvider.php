<?php

namespace App\Providers;

use Core\Foundation\Container;
use App\Config\Database;
use Modules\User\Repository\UserRepository;
use Modules\User\Repository\UserRepositoryInterface;

class DatabaseServiceProvider
{
    public function register(Container $container): void
    {
        // Register database connection
        $container->singleton('database', function() {
            return Database::getConnection();
        });

        // Register User module services
        $container->bind(
            UserRepositoryInterface::class,
            UserRepository::class
        );

        // Register UserService
        $container->bind(
            \Modules\User\Service\UserService::class,
            function(Container $container) {
                return new \Modules\User\Service\UserService(
                    $container->make(UserRepositoryInterface::class)
                );
            }
        );
    }

    public function boot(): void
    {
        // Boot database services if needed
    }
}