<?php

namespace Modules\User\Controller;

use Core\Http\BaseController;
use Core\Http\Request;
use Core\Http\Response;
use Core\Middleware\AuthMiddleware;
use Core\View\View;
use Modules\User\Service\UserService;
use Modules\User\DTO\RegisterUserDTO;
use Modules\User\DTO\LoginUserDTO;

class UserController extends BaseController
{
    private UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function loginPage(Request $request): Response
    {
        return View::render('login', [
            'title' => 'Login'
        ]);
    }

    public function login(Request $request): Response
    {
        $dto = new LoginUserDTO(
            $request->getPost('email', ''),
            $request->getPost('password', '')
        );

        $result = $this->userService->login($dto);

        return $this->handleResponse($request, $result, [
            'success_redirect' => View::url('/home/dashboard'),
            'success_message' => 'Welcome back! You have been logged in successfully.',
            'error_view' => 'login',
            'error_data' => ['title' => 'Login'],
            'old_input' => $this->getOldInput($request, ['email'])
        ]);
    }

    public function registerPage(Request $request): Response
    {
        return View::render('register', [
            'title' => 'Register'
        ]);
    }

    public function register(Request $request): Response
    {
        $dto = new RegisterUserDTO(
            $request->getPost('email', ''),
            $request->getPost('password', ''),
            $request->getPost('password_confirmation', '')
        );

        $result = $this->userService->register($dto);

        return $this->handleResponse($request, $result, [
            'success_redirect' => View::url('/users/login'),
            'success_message' => 'Registration successful! Please login to continue.',
            'error_view' => 'register',
            'error_data' => ['title' => 'Register'],
            'old_input' => $this->getOldInput($request, ['email'])
        ]);
    }

    public function profilePage(Request $request): Response
    {
        return View::render('profile', [
            'title' => 'Profile',
            'user' => $this->getCurrentUser()
        ]);
    }

    public function profile(Request $request): Response
    {
        return new Response(['user' => $this->getCurrentUser()], 200);
    }

    public function passwordPage(Request $request): Response
    {
        return View::render('password', [
            'title' => 'Change Password'
        ]);
    }

    public function password(Request $request): Response
    {
        // TODO: Implement password change logic
        return new Response([
            'message' => 'Password change functionality not implemented yet'
        ]);
    }

    public function logout(Request $request): Response
    {
        $result = $this->userService->logout();

        return $this->handleResponse($request, $result, [
            'success_redirect' => View::url('/users/login'),
            'error_view' => 'login',
            'error_data' => ['title' => 'Login'],
            'old_input' => $this->getOldInput($request, ['email'])
        ]);
    }
}