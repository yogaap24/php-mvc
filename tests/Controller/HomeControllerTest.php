<?php

namespace Yogaap\PHP\MVC\Tests\Controller;

use PHPUnit\Framework\TestCase;
use Ulid\Ulid;
use Yogaap\PHP\MVC\Config\Database;
use Yogaap\PHP\MVC\Domain\Session;
use Yogaap\PHP\MVC\Domain\User;
use Yogaap\PHP\MVC\Http\Controllers\HomeController;
use Yogaap\PHP\MVC\Repository\SessionRepository;
use Yogaap\PHP\MVC\Repository\UserRepository;
use Yogaap\PHP\MVC\Services\Session\SessionService;

class HomeControllerTest extends TestCase
{
    private HomeController $homeController;
    private UserRepository $userRepository;
    private SessionRepository $sessionRepository;
    
    private $sessionService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->sessionService = $this->createMock(SessionService::class);

        $this->homeController = new HomeController($this->sessionService);

        $this->sessionRepository = new SessionRepository(Database::getConnection());
        $this->sessionRepository->deleteAll();

        $this->userRepository = new UserRepository(Database::getConnection());
        $this->userRepository->deleteAll();
    }

    public function testIndex()
    {
        $this->homeController->index();

        $this->expectOutputRegex("[Welcome to Yogaap PHP MVC]");
        $this->expectOutputRegex("[Login]");
        $this->expectOutputRegex("[Register]");
    }

    public function testUserLogin()
    {
        $user = new User();
        $user->id =  Ulid::generate();
        $user->email = 'testHomeLog@test.mail';
        $user->password = password_hash('password', PASSWORD_BCRYPT);
        
        $this->userRepository->save($user);

        $session = new Session();
        $session->id = Ulid::generate();
        $session->user_id = $user->id;

        $this->sessionRepository->save($session);

        $_COOKIE[SessionService::$COOKIE_NAME] = $session->id;

        $this->homeController->dashboard();

        $this->expectOutputRegex("[Dashboard]");
    }

    protected function tearDown(): void
    {
        $this->sessionRepository->deleteAll();
        $this->userRepository->deleteAll();
    }
}