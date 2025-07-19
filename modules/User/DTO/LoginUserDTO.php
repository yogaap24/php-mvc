<?php

namespace Modules\User\DTO;

use Core\Service\ValidationService;

class LoginUserDTO
{
    public string $email;
    public string $password;

    public function __construct(string $email, string $password)
    {
        $this->email = $email;
        $this->password = $password;
    }

    public function validate(): array
    {
        return ValidationService::validate($this->toArray(), [
            'email' => 'required|email',
            'password' => 'required'
        ]);
    }

    public function toArray(): array
    {
        return [
            'email' => $this->email,
            'password' => $this->password
        ];
    }
}