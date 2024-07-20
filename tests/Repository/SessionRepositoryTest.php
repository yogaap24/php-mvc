<?php

namespace Yogaap\PHP\MVC\Tests\Repository;

use PHPUnit\Framework\TestCase;
use Ulid\Ulid;
use Yogaap\PHP\MVC\Config\Database;
use Yogaap\PHP\MVC\Domain\Session;
use Yogaap\PHP\MVC\Domain\User;
use Yogaap\PHP\MVC\Repository\SessionRepository;
use Yogaap\PHP\MVC\Repository\UserRepository;

class SessionRepositoryTest extends TestCase
{
    private SessionRepository $sessionRepository;
    private UserRepository $userRepository;

    protected function setUp():void
    {
        $this->userRepository = new UserRepository(Database::getConnection());
        $this->sessionRepository = new SessionRepository(Database::getConnection());

        $this->sessionRepository->deleteAll();
        $this->userRepository->deleteAll();

        $user = new User();
        $user->id = Ulid::generate();
        $user->email = 'sessionTestRepo@test.mail';
        $user->password = password_hash('password', PASSWORD_BCRYPT);
        $this->userRepository->save($user);
    }

    public function testSaveSuccess()
    {
        $user = $this->userRepository->findUser('sessionTestRepo@test.mail');

        $session = new Session();
        $session->id = Ulid::generate();
        $session->user_id = $user->id;

        $this->sessionRepository->save($session);

        $result = $this->sessionRepository->findSession($session->id);
        self::assertEquals($session->id, $result->id);
        self::assertEquals($session->user_id, $result->user_id);
    }

    public function testDeleteSessionSuccess()
    {
        $user = $this->userRepository->findUser('sessionTestRepo@test.mail');

        $session = new Session();
        $session->id = Ulid::generate();
        $session->user_id = $user->id;

        $this->sessionRepository->save($session);

        $result = $this->sessionRepository->findSession($session->id);
        self::assertEquals($session->id, $result->id);
        self::assertEquals($session->user_id, $result->user_id);

        $this->sessionRepository->deleteSession($session->id);

        $result = $this->sessionRepository->findSession($session->id);
        self::assertNull($result);
    }

    public function testFindSessionNotFound()
    {
        $result = $this->sessionRepository->findSession(Ulid::generate());
        self::assertNull($result);
    }
}