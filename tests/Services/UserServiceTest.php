<?php

namespace Yogaap\PHP\MVC\Tests\Services;

use PHPUnit\Framework\TestCase;
use Ulid\Ulid;
use Yogaap\PHP\MVC\Config\Database;
use Yogaap\PHP\MVC\Domain\User;
use Yogaap\PHP\MVC\Helper\ValidationException;
use Yogaap\PHP\MVC\Http\Requests\UserRequest;
use Yogaap\PHP\MVC\Repository\UserRepository;
use Yogaap\PHP\MVC\Services\User\UserService;

class UserServiceTest extends TestCase
{
    private UserService $userService;
    private UserRepository $userRepository;

    protected function setUp(): void
    {
        $connection = Database::getConnection();
        $this->userRepository = new UserRepository($connection);
        $this->userService = new UserService($this->userRepository);

        $this->userRepository->deleteAll();
    }

    public function testRegisterSuccess()
    {
        $request = new UserRequest();
        $request->id = Ulid::generate();
        $request->email = 'userService@test.email';
        $request->password = password_hash('password', PASSWORD_BCRYPT);
        $request->confirmPassword = $request->password;

        $response = $this->userService->store($request);

        self::assertInstanceOf(User::class, $response);

        self::assertEquals($request->id, $response->id);
        self::assertEquals($request->email, $response->email);
        self::assertNotEquals($request->password, $response->password);

        self::assertTrue(password_verify($request->password, $response->password));
    }

    public function testRegisterFailed()
    {
        $this->expectException(ValidationException::class);

        $request = new UserRequest();
        $request->email = "";
        $request->password = "";
        $request->confirmPassword = "1";

        $this->userService->store($request);
    }

    public function testRegisterDuplicate()
    {
        $id = Ulid::generate();
        $email = "RegDup@mail.example";
        $password = password_hash('password', PASSWORD_BCRYPT);

        $request = new UserRequest();
        $request->id = $id;
        $request->email = $email;
        $request->password = $password;
        $request->confirmPassword = $password;

        $this->userService->store($request);

        $this->expectException(ValidationException::class);

        $request2 = new UserRequest();
        $request2->id = $id;
        $request2->email = $email;
        $request2->password = $password;
        $request2->confirmPassword = $password;

        $this->userService->store($request2);
    }

    public function testLoginNotFound()
    {
        $this->expectException(ValidationException::class);

        $request = new UserRequest();
        $request->id = Ulid::generate();
        $request->email = 'LogNotFound@test.com';
        $request->password = password_hash('password', PASSWORD_BCRYPT);
        $request->confirmPassword = $request->password;

        $this->userService->login($request);
    }

    public function testLoginInvalidPassword()
    {
        $request = new UserRequest();
        $request->id = Ulid::generate();
        $request->email = 'LogInvalTest@test.com';
        $request->password = password_hash('password', PASSWORD_BCRYPT);
        $request->confirmPassword = $request->password;

        $this->userService->store($request);

        $this->expectException(ValidationException::class);

        $request2 = new UserRequest();
        $request2->id = Ulid::generate();
        $request2->email = $request->email;
        $request2->password = password_hash('password2', PASSWORD_BCRYPT);

        $this->userService->login($request2);
    }

    public function testLoginSuccess()
    {
        $request = new UserRequest();
        $request->id = Ulid::generate();
        $request->email = 'LogSucces@test.com';
        $request->password = password_hash('password', PASSWORD_BCRYPT);
        $request->confirmPassword = $request->password;

        $this->userService->store($request);

        $response = $this->userService->login($request);

        self::assertInstanceOf(User::class, $response);
        self::assertEquals($request->id, $response->id);
        self::assertTrue(password_verify($request->password, $response->password));
    }

    public function testUpdateSuccess()
    {
        $password = password_hash('password', PASSWORD_BCRYPT);

        $user = new User();
        $user->id = Ulid::generate();
        $user->email = 'UpBfrSuccess@test.mail';
        $user->password = password_hash('password', PASSWORD_BCRYPT);

        $this->userRepository->save($user);

        $request = new UserRequest();
        $request->id = $user->id;
        $request->email = 'UpAftSuccess@test.mail';
        $request->oldPassword = 'password';
        $request->newPassword = 'password_new';
        $request->confirmPassword = $request->newPassword;

        $this->userService->update($request, $user->id);

        $result = $this->userRepository->findUser($user->id);

        self::assertEquals($request->email, $result->email);
        self::assertTrue(password_verify($request->newPassword, $result->password));
    }

    public function testUpdateValidationFailed()
    {
        $this->expectException(ValidationException::class);

        $request = new UserRequest();
        $request->id = Ulid::generate();
        $request->email = "";
        $request->password = "";
        $request->confirmPassword = "1";

        $requestUpPassword = new UserRequest();
        $requestUpPassword->id = Ulid::generate();
        $requestUpPassword->oldPassword = "password";
        $requestUpPassword->newPassword = "1";

        $this->userService->update($request, $request->id);
    }

    public function testUpdateNotFound()
    {
        $this->expectException(ValidationException::class);

        $request = new UserRequest();
        $request->id = Ulid::generate();
        $request->email = 'UpNotFnd@test.mail';

        $reqUpPassword = new UserRequest();
        $reqUpPassword->id = Ulid::generate();
        $reqUpPassword->oldPassword = "password";
        $reqUpPassword->newPassword = "password_new";

        $this->userService->update($request, $request->id);
    }

    public function testUpdatePasswordWrongOldPassword()
    {
        $this->expectException(ValidationException::class);

        $user = new User();
        $user->id = Ulid::generate();
        $user->email = 'wrongOldPass@mail.test';
        $user->password = password_hash('password', PASSWORD_BCRYPT);

        $this->userRepository->save($user);

        $request = new UserRequest();
        $request->id = $user->id;
        $request->oldPassword = 'wrong_password';
        $request->newPassword = 'password_new';

        $this->userService->update($request, $user->id);
    }

    protected function tearDown(): void
    {
        $this->userRepository->deleteAll();
    }
}