<?php

namespace Yogaap\PHP\MVC\Http\Controllers;

use Yogaap\PHP\MVC\App\View;
use Yogaap\PHP\MVC\Config\Database;
use Yogaap\PHP\MVC\Repository\SessionRepository;
use Yogaap\PHP\MVC\Repository\UserRepository;
use Yogaap\PHP\MVC\Services\Session\SessionService;

class HomeController
{

    private SessionService $sessionService;

    public function __construct()
    {
        $connection = Database::getConnection();
        $sessionRepository = new SessionRepository($connection);
        $userRepository = new UserRepository($connection);
        $this->sessionService = new SessionService($sessionRepository, $userRepository);
    }

    function index() : void
    {
        $response = [
            "title" => "PHP MVC Stupid Simple Framework",
        ];

        View::render('Home/index', $response);
    }

    function dashboard() : void
    {
        View::render('Home/dashboard', [
            "title" => "Dashboard"
        ]);
    }
}