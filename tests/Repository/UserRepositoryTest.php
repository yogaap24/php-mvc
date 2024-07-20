<?php

namespace Yogaap\PHP\MVC\Tests\Repository;

use PHPUnit\Framework\TestCase;
use Ulid\Ulid;
use Yogaap\PHP\MVC\Config\Database;
use Yogaap\PHP\MVC\Domain\User;
use Yogaap\PHP\MVC\Repository\UserRepository;

class UserRepositoryTest extends TestCase
{
    private UserRepository  $userRepository;

    protected function setUp(): void
    {
        $this->userRepository = new UserRepository(Database::getConnection());
        $this->userRepository->deleteAll();
    }

    public function testSaveSuccess()
    {
        $user = new User();
        $user->id = Ulid::generate();
        $user->email = 'userRepo@test.email';
        $user->password = password_hash('password', PASSWORD_BCRYPT);

        $this->userRepository->save($user);

        $result = $this->userRepository->findUser($user->id);

        self::assertNotNull($result);
        self::assertEquals($user->id, $result->id);
        self::assertEquals($user->email, $result->email);
        self::assertEquals($user->password, $result->password);
    }

    public function testFindUserNotFound()
    {
        $result = $this->userRepository->findUser(Ulid::generate());

        self::assertNull($result);
    }

    public function testUpdateSuccess()
    {
        $user = new User();
        $user->id = Ulid::generate();
        $user->email = 'testBfrUpUsrRepo@test.mail';
        $user->password = password_hash('password', PASSWORD_BCRYPT);

        $this->userRepository->save($user);

        $user->email = 'testAftUpUsrRepo@test.mail';
        $this->userRepository->update($user, $user->id);

        $result = $this->userRepository->findUser($user->id);

        self::assertEquals($user->id, $result->id);
        self::assertEquals($user->email, $result->email);
        self::assertEquals($user->password, $result->password);
    }

    protected function tearDown(): void
    {
        $this->userRepository->deleteAll();
    }
}
