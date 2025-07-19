<?php

namespace Modules\User\DTO;

use Core\Service\ValidationService;

class RegisterUserDTO
{
    public string $email;
    public string $password;
    public string $passwordConfirmation;

    public function __construct(string $email, string $password, string $passwordConfirmation)
    {
        $this->email = $email;
        $this->password = $password;
        $this->passwordConfirmation = $passwordConfirmation;
    }

    public function validate(): array
    {
        return ValidationService::validate($this->toArray(), [
            'email' => 'required|email',
            'password' => 'required|min:6|confirmed',
            'password_confirmation' => 'required'
        ]);
    }

    public function toArray(): array
    {
        return [
            'email' => $this->email,
            'password' => $this->password,
            'password_confirmation' => $this->passwordConfirmation
        ];
    }
}