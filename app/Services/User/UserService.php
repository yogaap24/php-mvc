<?php

namespace Yogaap\PHP\MVC\Services\User;

use Yogaap\PHP\MVC\Config\Database;
use Yogaap\PHP\MVC\Domain\User;
use Yogaap\PHP\MVC\Helper\ValidationException;
use Yogaap\PHP\MVC\Http\Requests\UserRequest;
use Yogaap\PHP\MVC\Repository\UserRepository;

class UserService
{

    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function store(UserRequest $request): User
    {
        $errors = $request->validate();
        if (!empty($errors)) {
            throw new ValidationException(json_encode($errors));
        }

        try {
            Database::beginTransaction();
            $user = $this->userRepository->findUser($request->email);
            if ($user) {
                throw new ValidationException('User already exists.');
            }

            $user = new User();
            $user->id = $request->id;
            $user->email = $request->email;
            $user->password = password_hash($request->password, PASSWORD_BCRYPT);

            $user = $this->userRepository->save($user);
            Database::commitTransaction();

            return $user;
        } catch (\Exception $exception) {
            Database::rollbackTransaction();
            throw $exception;
        }
    }

    public function login(UserRequest $request): User
    {
        $errors = $request->validate();
        if (!empty($errors)) {
            throw new ValidationException(json_encode($errors));
        }

        try {
            $user = $this->userRepository->findUser($request->email);
            if (!$user) {
                throw new ValidationException('User not found.');
            }

            if (!password_verify($request->password, $user->password)) {
                throw new ValidationException('Password is incorrect.');
            }

            return $user;
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    public function update(UserRequest $request, string $id): User
    {
        $errors = $request->validate();
        if (!empty($errors)) {
            throw new ValidationException(json_encode($errors));
        }

        try {
            Database::beginTransaction();
            $user = $this->userRepository->findUser($id);
            if (!$user) {   
                throw new ValidationException('User not found.');
            }

            if ($request->oldPassword) {
                if (!password_verify($request->oldPassword, $user->password)) {
                    throw new ValidationException('Old password is incorrect.');
                }

                $user->password = password_hash($request->newPassword, PASSWORD_BCRYPT);
            }

            $user->email = ($request->email) ? $request->email : $user->email;
            $user = $this->userRepository->update($user, $id);

            Database::commitTransaction();

            return $user;
        } catch (\Exception $exception) {
            Database::rollbackTransaction();
            throw $exception;
        }
    }
}