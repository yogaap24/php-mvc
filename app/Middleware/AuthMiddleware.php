<?php

namespace Yogaap\PHP\MVC\Middleware;

use Yogaap\PHP\MVC\App\View;
use Yogaap\PHP\MVC\Config\Database;
use Yogaap\PHP\MVC\Repository\SessionRepository;
use Yogaap\PHP\MVC\Repository\UserRepository;
use Yogaap\PHP\MVC\Services\Session\SessionService;

class AuthMiddleware implements Middleware
{
    private SessionService $sessionService;

    public function __construct()
    {
        $connection = Database::getConnection();
        $sessionRepository = new SessionRepository($connection);
        $userRepository = new UserRepository($connection);
        $this->sessionService = new SessionService($sessionRepository, $userRepository);
    }
    
    function before() : void
    {
        $user = $this->sessionService->current();
        $path = $_SERVER['REQUEST_URI'];

        $allowRoutes = ['/users/login', '/users/register'];

        if (!$user && !in_array($path, $allowRoutes)) {
            View::redirect('/users/login', [
                "error" => "You need to login first"
            ]);
        }

        if ($user && in_array($path, $allowRoutes)) {
            View::redirect('/home/dashboard');
        }
    }
}