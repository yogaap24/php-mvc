<?php

namespace Yogaap\PHP\MVC\Http\Controllers;

use Ulid\Ulid;
use Yogaap\PHP\MVC\App\View;
use Yogaap\PHP\MVC\Config\Database;
use Yogaap\PHP\MVC\Helper\ValidationException;
use Yogaap\PHP\MVC\Http\Requests\UserRequest;
use Yogaap\PHP\MVC\Repository\SessionRepository;
use Yogaap\PHP\MVC\Repository\UserRepository;
use Yogaap\PHP\MVC\Services\Session\SessionService;
use Yogaap\PHP\MVC\Services\User\UserService;

class UserController
{
    private UserService $userService;
    private SessionService $sessionService;

    public function __construct()
    {
        $connection = Database::getConnection();
        $userRepository = new UserRepository($connection);
        $this->userService = new UserService($userRepository);

        $sessionRepository = new SessionRepository($connection);
        $this->sessionService = new SessionService($sessionRepository, $userRepository);
    }

    public function registerPage()
    {
        View::render('User/register', [
            "title" => "Register new User",
        ]);
    }

    public function register()
    {
        $request = new UserRequest();
        $request->id = Ulid::generate();
        $request->email = $_POST['email'];
        $request->password = $_POST['password'];
        $request->confirmPassword = $_POST['confirm_password'];

        try {
            $this->userService->store($request);
            View::redirect('/users/login');
        } catch (ValidationException $exception) {
            View::render('User/register', [
                "title" => "Register new User",
                "error" => $exception->getMessage()
            ]);
        }
    }

    public function loginPage()
    {
        View::render('User/login', [
            "title" => "Login to your account"
        ]);
    }

    public function login()
    {
        $request = new UserRequest();
        $request->email = $_POST['email'];
        $request->password = $_POST['password'];

        try {
            $response = $this->userService->login($request);
            $this->sessionService->store($response->id);
            View::redirect('/home/dashboard');
        } catch (ValidationException $exception) {
            View::render('User/login', [
                "title" => "Login to your account",
                "error" => $exception->getMessage()
            ]);
        }
    }

    public function profilePage()
    {
        $user = $this->sessionService->current();
        $data = [
            "title" => "Profile",
            "id" => $user->id,
            "email" => $user->email
        ];

        View::render('User/profile', $data);
    }

    public function profile(string $id)
    {
        $request = new UserRequest();
        $request->email = $_POST['email'];

        try {
            $this->userService->update($request, $id);
            View::redirect('/users/profile', [
                "success" => "Profile has been updated"
            ]);
        } catch (ValidationException $exception) {
            View::render('User/profile', [
                "title" => "Profile",
                "error" => $exception->getMessage()
            ]);
        }
    }

    public function passwordPage()
    {
        $user = $this->sessionService->current();
        $data = [
            "title" => "Password",
            "id" => $user->id
        ];

        View::render('User/password', $data);
    }

    public function password(string $id)
    {
        $request = new UserRequest();
        $request->oldPassword     = $_POST['old_password'];
        $request->newPassword     = $_POST['new_password'];
        $request->confirmPassword = $_POST['confirm_password'];

        try {
            $this->userService->update($request, $id);
            View::redirect('/users/password', [
                "success" => "Password has been changed"
            ]);
        } catch (ValidationException $exception) {
            View::render('User/password', [
                "title" => "Password",
                "error" => $exception->getMessage()
            ]);
        }
    }

    public function logout()
    {
        $this->sessionService->destroy();
        View::redirect('/users/login', [
            "success" => "You have been logged out"
        ]);
    }
}
