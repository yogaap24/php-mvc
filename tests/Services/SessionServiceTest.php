<?php

namespace Yogaap\PHP\MVC\Tests\Services;

require_once __DIR__ . '/../../app/Helper/Helper.php';

use PHPUnit\Framework\TestCase;
use Ulid\Ulid;
use Yogaap\PHP\MVC\Config\Database;
use Yogaap\PHP\MVC\Domain\Session;
use Yogaap\PHP\MVC\Domain\User;
use Yogaap\PHP\MVC\Repository\SessionRepository;
use Yogaap\PHP\MVC\Repository\UserRepository;
use Yogaap\PHP\MVC\Services\Session\SessionService;

class SessionServiceTest extends TestCase
{
    private SessionService $sessionService;
    private SessionRepository $sessionRepository;
    private UserRepository $userRepository;

    protected function setUp():void
    {
        $this->sessionRepository = new SessionRepository(Database::getConnection());
        $this->userRepository = new UserRepository(Database::getConnection());
        $this->sessionService = new SessionService($this->sessionRepository, $this->userRepository);

        $this->sessionRepository->deleteAll();
        $this->userRepository->deleteAll();

        $user = new User();
        $user->id = "session-test-id";
        $user->email = 'seesionServTest@test.mail';
        $user->password = password_hash('password', PASSWORD_BCRYPT);

        $this->userRepository->save($user);
    }

    public function testStore()
    {
        $session = $this->sessionService->store("session-test-id");

        $this->expectOutputRegex("[X-YOGAAP-SESSION: $session->id]");

        $result = $this->sessionRepository->findSession($session->id);
        $this->assertEquals("session-test-id", $result->user_id);
    }

    public function testDestroy()
    {
        $session = new Session();
        $session->id = Ulid::generate();
        $session->user_id = "session-test-id";

        $this->sessionRepository->save($session);

        $_COOKIE[SessionService::$COOKIE_NAME] = $session->id;

        $this->sessionService->destroy();

        $this->expectOutputRegex("[X-YOGAAP-SESSION: ]");

        $result = $this->sessionRepository->findSession($session->id);
        self::assertNull($result);
    }

    public function testCurrent()
    {
        $session = new Session();
        $session->id = Ulid::generate();
        $session->user_id = "session-test-id";

        $this->sessionRepository->save($session);

        $_COOKIE[SessionService::$COOKIE_NAME] = $session->id;

        $result = $this->sessionService->current();

        $this->assertEquals($session->user_id, $result->id);
    }

    protected function tearDown():void
    {
        $this->sessionRepository->deleteAll();
        $this->userRepository->deleteAll();
    }
}