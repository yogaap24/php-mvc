<?php

namespace Yogaap\PHP\MVC\Tests\Middleware {

    require_once __DIR__ . '/../../app/Helper/Helper.php';

    use PHPUnit\Framework\TestCase;
    use Ulid\Ulid;
    use Yogaap\PHP\MVC\Config\Database;
    use Yogaap\PHP\MVC\Domain\Session;
    use Yogaap\PHP\MVC\Domain\User;
    use Yogaap\PHP\MVC\Middleware\AuthMiddleware;
    use Yogaap\PHP\MVC\Repository\SessionRepository;
    use Yogaap\PHP\MVC\Repository\UserRepository;
    use Yogaap\PHP\MVC\Services\Session\SessionService;

    class AuthMiddlewareTest extends TestCase
    {
        private AuthMiddleware $authMiddleware;
        private UserRepository $userRepository;
        private SessionRepository $sessionRepository;

        protected function setUp(): void
        {
            $this->authMiddleware = new AuthMiddleware();

            $this->sessionRepository = new SessionRepository(Database::getConnection());
            $this->sessionRepository->deleteAll();

            $this->userRepository = new UserRepository(Database::getConnection());
            $this->userRepository->deleteAll();
        }

        public function testBeforeGuest()
        {
            $_SERVER['REQUEST_URI'] = '/tests/auth';

            $this->authMiddleware->before();

            $this->expectOutputRegex("[Location: /users/login]");
            $this->expectOutputRegex("[You need to login first]");
        }

        public function testBeforeLoginUser()
        {
            $user = new User();
            $user->id = Ulid::generate();
            $user->email = "testBefMidd@test.mail";
            $user->password = password_hash('password', PASSWORD_BCRYPT);

            $this->userRepository->save($user);

            $session = new Session();
            $session->id = Ulid::generate();
            $session->user_id = $user->id;

            $this->sessionRepository->save($session);

            $_COOKIE[SessionService::$COOKIE_NAME] = $session->id;
            $_SERVER['REQUEST_URI'] = '/users/login';

            $this->authMiddleware->before();

            $this->expectOutputRegex("[Location: /home/dashboard]");
        }

        protected function tearDown(): void
        {
            $this->sessionRepository->deleteAll();
            $this->userRepository->deleteAll();
        }
    }
}
