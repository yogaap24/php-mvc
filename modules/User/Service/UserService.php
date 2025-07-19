<?php

namespace Modules\User\Service;

use Core\Service\BaseService;
use Modules\User\Repository\UserRepositoryInterface;
use Modules\User\DTO\RegisterUserDTO;
use Modules\User\DTO\LoginUserDTO;
use Modules\User\Entity\User;
use Core\Middleware\AuthMiddleware;

class UserService extends BaseService
{
    private UserRepositoryInterface $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function register(RegisterUserDTO $dto)
    {
        try {
            // Validation
            $errors = $dto->validate();
            if (!empty($errors)) {
                return $this->sendError($errors, 'Validation failed', 400);
            }

            // Check if user exists
            if ($this->userRepository->findByEmail($dto->email)) {
                return $this->sendError(null, 'User already exists', 409);
            }

            // Create user
            $user = new User();
            $user->setEmail($dto->email);
            $user->setPassword($dto->password);

            $this->userRepository->save($user);

            return $this->sendSuccess($user->toArray(), 'User registered successfully', 201);
        } catch (\Exception $e) {
            return $this->sendError(null, $e->getMessage(), 500);
        }
    }

    public function login(LoginUserDTO $dto)
    {
        try {
            // Validation
            $errors = $dto->validate();
            if (!empty($errors)) {
                return $this->sendError($errors, 'Validation failed', 400);
            }

            // Find user
            $user = $this->userRepository->findByEmail($dto->email);
            if (!$user || !$user->verifyPassword($dto->password)) {
                return $this->sendError(null, 'Invalid credentials', 401);
            }

            // Login user
            AuthMiddleware::login($user->toArray());

            return $this->sendSuccess($user->toArray(), 'Login successful');
        } catch (\Exception $e) {
            return $this->sendError(null, $e->getMessage(), 500);
        }
    }

    public function logout()
    {
        try {
            AuthMiddleware::logout();
            return $this->sendSuccess(null, 'Logout successful');
        } catch (\Exception $e) {
            return $this->sendError(null, $e->getMessage(), 500);
        }
    }
}