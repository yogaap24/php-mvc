<?php

namespace Yogaap\PHP\MVC\Http\Requests;

use Yogaap\PHP\MVC\Helper\ValidationRules;

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
        $validator = new ValidationRules();
        $data = $this->toArray();

        $rules = $this->getRules();
        $messages = $this->getMessages();

        $errors = $validator->validate($data, $rules, $messages);

        // Flatten errors array to match original format
        $flatErrors = [];
        foreach ($errors as $field => $fieldErrors) {
            $flatErrors[$field] = $fieldErrors[0]; // Take first error only
        }

        return $flatErrors;
    }

    private function getRules(): array
    {
        $rules = [];

        // Email validation
        if ($this->email !== null) {
            $rules['email'] = ['required', 'email', 'max:255'];
        }

        // Password validation for registration/login
        if ($this->password !== null) {
            $rules['password'] = ['required', 'min:6'];

            if ($this->confirmPassword !== null) {
                $rules['password'][] = 'same:confirmPassword';
            }
        }

        // Old password validation
        if ($this->oldPassword !== null) {
            $rules['oldPassword'] = ['required', 'min:6'];
        }

        // New password validation
        if ($this->newPassword !== null) {
            $rules['newPassword'] = ['required', 'min:6', 'different:oldPassword'];

            if ($this->confirmPassword !== null) {
                $rules['newPassword'][] = 'same:confirmPassword';
            }
        }

        return $rules;
    }

    private function getMessages(): array
    {
        return [
            'email.required' => 'Email is required.',
            'email.email' => 'Please enter a valid email address.',
            'email.max' => 'Email must not exceed 255 characters.',
            'password.required' => 'Password is required.',
            'password.min' => 'Password must be at least 6 characters long.',
            'password.same' => 'Password confirmation does not match.',
            'oldPassword.required' => 'Current password is required.',
            'oldPassword.min' => 'Current password must be at least 6 characters long.',
            'newPassword.required' => 'New password is required.',
            'newPassword.min' => 'New password must be at least 6 characters long.',
            'newPassword.different' => 'New password must be different from current password.',
            'newPassword.same' => 'New password confirmation does not match.',
        ];
    }

    private function toArray(): array
    {
        return [
            'id' => $this->id,
            'email' => $this->email,
            'password' => $this->password,
            'confirmPassword' => $this->confirmPassword,
            'oldPassword' => $this->oldPassword,
            'newPassword' => $this->newPassword,
        ];
    }
}
