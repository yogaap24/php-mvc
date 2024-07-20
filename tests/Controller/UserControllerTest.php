<?php

namespace Yogaap\PHP\MVC\Tests\Controller {

    require_once __DIR__ . '/../../app/Helper/Helper.php';

    use PHPUnit\Framework\TestCase;
    use Ulid\Ulid;
    use Yogaap\PHP\MVC\Config\Database;
    use Yogaap\PHP\MVC\Domain\Session;
    use Yogaap\PHP\MVC\Domain\User;
    use Yogaap\PHP\MVC\Http\Controllers\UserController;
    use Yogaap\PHP\MVC\Repository\SessionRepository;
    use Yogaap\PHP\MVC\Repository\UserRepository;
    use Yogaap\PHP\MVC\Services\Session\SessionService;

    class UserControllerTest extends TestCase
    {
        
        private SessionRepository $sessionRepository;
        private UserController $userController;
        private UserRepository $userRepository;

        protected function setUp(): void
        {
            $this->userController = new UserController();

            $this->sessionRepository = new SessionRepository(Database::getConnection());
            $this->sessionRepository->deleteAll();

            $this->userRepository = new UserRepository(Database::getConnection());
            $this->userRepository->deleteAll();
        }

        public function testRegisterPage()
        {
            $this->userController->registerPage();

            $this->expectOutputRegex("[Register]");
            $this->expectOutputRegex("[Register new User]");
            $this->expectOutputRegex("[Email]");
            $this->expectOutputRegex("[Password]");
            $this->expectOutputRegex("[Confirm Password]");
        }

        public function testRegisterSuccess()
        {
            $_POST['id'] = Ulid::generate();
            $_POST['email'] = 'RegSucCon@mail.example';
            $_POST['password'] = password_hash('password', PASSWORD_BCRYPT);
            $_POST['confirm_password'] = $_POST['password'];

            $this->userController->register();

            $this->expectOutputRegex("[Location: /users/login]");
        }

        public function testRegisterValidationError()
        {
            $_POST['email'] = '';
            $_POST['password'] = '';
            $_POST['confirm_password'] = 'not match';

            $this->userController->register();

            $this->expectOutputRegex("[Register]");
            $this->expectOutputRegex("[Register new User]");
            $this->expectOutputRegex("[Email]");
            $this->expectOutputRegex("[Password]");
            $this->expectOutputRegex("[Confirm Password]");

            $this->expectOutputRegex("[Email is required.]");
            $this->expectOutputRegex("[Password is required.]");
        }

        public function testRegisterUserAlreadyExists()
        {

            $id = Ulid::generate();
            $email = "RegDupCon@mail.example";
            $password = password_hash('password', PASSWORD_BCRYPT);

            $user = new User();
            $user->id = $id;
            $user->email = $email;
            $user->password = $password;

            $this->userRepository->save($user);

            $_POST['id'] =
            $_POST['email'] = $email;
            $_POST['password'] = $password;
            $_POST['confirm_password'] = $password;

            $this->userController->register();

            $this->expectOutputRegex("[Register]");
            $this->expectOutputRegex("[Register new User]");
            $this->expectOutputRegex("[Email]");
            $this->expectOutputRegex("[Password]");
            $this->expectOutputRegex("[Confirm Password]");
            $this->expectOutputRegex("[User already exists.]");
        }

        public function testLoginPage()
        {
            $this->userController->loginPage();

            $this->expectOutputRegex("[Login]");
            $this->expectOutputRegex("[Login to your account]");
            $this->expectOutputRegex("[Email]");
            $this->expectOutputRegex("[Password]");
        }

        public function testLoginSuccess()
        {
            $user = new User();
            $user->id = Ulid::generate();
            $user->email = "testLogSuc@test.mail";
            $user->password = password_hash('password', PASSWORD_BCRYPT);

            $this->userRepository->save($user);

            $_POST['email']    = $user->email;
            $_POST['password'] = 'password';

            $this->userController->login();

            $this->expectOutputRegex("[Location: /home/dashboard]");
        }

        public function testLoginValidationError()
        {
            $_POST['email'] = '';
            $_POST['password'] = '';

            $this->userController->login();

            $this->expectOutputRegex("[Login]");
            $this->expectOutputRegex("[Login to your account]");
            $this->expectOutputRegex("[Email]");
            $this->expectOutputRegex("[Password]");

            $this->expectOutputRegex("[Email is required.]");
            $this->expectOutputRegex("[Password is required.]");
        }

        public function testLoginNotFound()
        {
            $_POST['email'] = 'notFoundUser@test.mail';
            $_POST['password'] = 'password';

            $this->userController->login();

            $this->expectOutputRegex("[Login]");
            $this->expectOutputRegex("[Login to your account]");
            $this->expectOutputRegex("[Email]");
            $this->expectOutputRegex("[Password]");

            $this->expectOutputRegex("[User not found.]");
        }

        public function testLoginInvalidPassword()
        {
            $user = new User();
            $user->id = Ulid::generate();
            $user->email = "testLogInvalPass@test.mail";
            $user->password = password_hash('password', PASSWORD_BCRYPT);

            $this->userRepository->save($user);

            $_POST['email']    = $user->email;
            $_POST['password'] = 'invalid';

            $this->userController->login();

            $this->expectOutputRegex("[Login]");
            $this->expectOutputRegex("[Login to your account]");
            $this->expectOutputRegex("[Email]");
            $this->expectOutputRegex("[Password]");

            $this->expectOutputRegex("[Password is incorrect.]");
        }

        public function testProfilePage()
        {
            
            $user = new User();
            $user->id = Ulid::generate();
            $user->email = "testProfPage@test.mail";
            $user->password = password_hash('password', PASSWORD_BCRYPT);

            $this->userRepository->save($user);

            $session = new Session();
            $session->id = Ulid::generate();
            $session->user_id = $user->id;

            $this->sessionRepository->save($session);

            $_COOKIE[SessionService::$COOKIE_NAME] = $session->id;

            $this->userController->profilePage();

            $this->expectOutputRegex("[Profile]");
            $this->expectOutputRegex("[Id]");
            $this->expectOutputRegex("[Email]");
            $this->expectOutputRegex("[testProfPage@test.mail]");
        }

        public function testPostUpdateProfileSuccess()
        {
            $user = new User();
            $user->id = Ulid::generate();
            $user->email = "testUpProfSucc@test.mail";
            $user->password = password_hash('password', PASSWORD_BCRYPT);

            $this->userRepository->save($user);

            $session = new Session();
            $session->id = Ulid::generate();
            $session->user_id = $user->id;

            $this->sessionRepository->save($session);

            $_COOKIE[SessionService::$COOKIE_NAME] = $session->id;

            $_POST['email'] = 'testUpProfSuccAf@test.mail';

            $this->userController->profile($user->id);

            $this->expectOutputRegex("[Location: /users/profile]");
            $this->expectOutputRegex("[Profile has been updated.]");

            $result = $this->userRepository->findUser($user->id);
            $this->assertEquals("testUpProfSuccAf@test.mail", $result->email);
        }

        public function testPostUpdateProfileFail()
        {
            $user = new User();
            $user->id = Ulid::generate();
            $user->email = "testUpProfFail@test.mail";
            $user->password = password_hash('password', PASSWORD_BCRYPT);

            $this->userRepository->save($user);

            $session = new Session();
            $session->id = Ulid::generate();
            $session->user_id = $user->id;

            $this->sessionRepository->save($session);

            $_COOKIE[SessionService::$COOKIE_NAME] = $session->id;

            $_POST['email'] = '';
            $this->userController->profile($user->id);

            $this->expectOutputRegex("[Profile]");
            $this->expectOutputRegex("[Id]");
            $this->expectOutputRegex("[Email]");
            $this->expectOutputRegex("[Email is required.]");
        }

        public function testPasswordPage()
        {
            $user = new User();
            $user->id = Ulid::generate();
            $user->email = "testPassPage@mail.test";
            $user->password = password_hash('password', PASSWORD_BCRYPT);

            $this->userRepository->save($user);

            $session = new Session();
            $session->id = Ulid::generate();
            $session->user_id = $user->id;

            $this->sessionRepository->save($session);

            $_COOKIE[SessionService::$COOKIE_NAME] = $session->id;

            $this->userController->passwordPage();

            $this->expectOutputRegex("[Id]");
            $this->expectOutputRegex("[Password]");
            $this->expectOutputRegex("[Old Password]");
            $this->expectOutputRegex("[New Password]");
            $this->expectOutputRegex("[Confirm New Password]");
        }


        public function testUpdatePasswordSuccess()
        {
            $user = new User();
            $user->id = Ulid::generate();
            $user->email = "testUpPasssucc@test.mail";
            $user->password = password_hash('password', PASSWORD_BCRYPT);

            $this->userRepository->save($user);

            $session = new Session();
            $session->id = Ulid::generate();
            $session->user_id = $user->id;

            $this->sessionRepository->save($session);

            $_COOKIE[SessionService::$COOKIE_NAME] = $session->id;

            $_POST['old_password'] = 'password';
            $_POST['new_password'] = 'newpassword';
            $_POST['confirm_password'] = 'newpassword';

            $this->userController->password($user->id);

            $this->expectOutputRegex("[Location: /users/password]");
            $this->expectOutputRegex("[Password has been changed.]");

            $result = $this->userRepository->findUser($user->id);

            $this->assertTrue(password_verify('newpassword', $result->password));
            self::assertNotEquals($user->password, $result->password);
        }

        public function testUpdatePasswordFail()
        {
            $user = new User();
            $user->id = Ulid::generate();
            $user->email = "testUpPassFail@test.mail";
            $user->password = password_hash('password', PASSWORD_BCRYPT);

            $this->userRepository->save($user);

            $session = new Session();
            $session->id = Ulid::generate();
            $session->user_id = $user->id;

            $this->sessionRepository->save($session);

            $_COOKIE[SessionService::$COOKIE_NAME] = $session->id;

            $_POST['old_password'] = 'invalid';
            $_POST['new_password'] = 'newpassword';
            $_POST['confirm_password'] = 'newppadword';

            $this->userController->password($user->id);

            $this->expectOutputRegex("[Id]");
            $this->expectOutputRegex("[Password]");
            $this->expectOutputRegex("[Old Password]");
            $this->expectOutputRegex("[New Password]");
            $this->expectOutputRegex("[Confirm New Password]");
            $this->expectOutputRegex("[Old password is incorrect.]");
            $this->expectOutputRegex("[New password does not match.]");
        }

        public function testLogout()
        {
            $user = new User();
            $user->id = Ulid::generate();
            $user->email = "testLogOut@test.mail";
            $user->password = password_hash('password', PASSWORD_BCRYPT);

            $this->userRepository->save($user);

            $session = new Session();
            $session->id = Ulid::generate();
            $session->user_id = $user->id;

            $this->sessionRepository->save($session);

            $_COOKIE[SessionService::$COOKIE_NAME] = $session->id;

            $this->userController->logout();

            $this->expectOutputRegex("[Location: /users/login]");
            $this->expectOutputRegex("[X-YOGAAP-SESSION: ]");
        }

        protected function tearDown(): void
        {
            $this->sessionRepository->deleteAll();
            $this->userRepository->deleteAll();
        }
    }
}
