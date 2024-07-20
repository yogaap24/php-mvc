<?php

namespace Yogaap\PHP\MVC\Http\Requests;

class UserRequest
{
    public ?string $id = null;
    public ?string $email = null;
    public ?string $password = null;
    public ?string $confirmPassword = null;
    public ?string $oldPassword = null;
    public ?string $newPassword = null;

    public function validate(): array
    {
        $errors = [];
        // Validate Email
        if (!is_null($this->email) && empty($this->email)) {
            $errors['email'] = 'Email is required.';
        } elseif (!is_null($this->email) && !filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Invalid email format.';
        }

        // Validate Password
        if (!is_null($this->password) && empty($this->password)) {
            $errors['password'] = 'Password is required.';
        } elseif (!is_null($this->password) && strlen($this->password) < 6) {
            $errors['password'] = 'Password must be at least 6 characters long.';
        } elseif (!is_null($this->confirmPassword) && !is_null($this->password) && $this->password !== $this->confirmPassword) {
            $errors['password'] = 'Password does not match.';
        } elseif (!is_null($this->newPassword) && strlen($this->newPassword) < 6) {
            $errors['password'] = 'New password must be at least 6 characters long.';
        } elseif (!is_null($this->newPassword) && $this->newPassword === $this->oldPassword) {
            $errors['password'] = 'New password must be different from the old password.';
        } elseif (!is_null($this->newPassword) && !is_null($this->confirmPassword) && $this->newPassword !== $this->confirmPassword) {
            $errors['password'] = 'New password does not match.';
        }

        return $errors;
    }
}
