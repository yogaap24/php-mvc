<?php

namespace Yogaap\PHP\MVC\Helper;

class ValidationRules
{
    private array $messages = [];
    private array $customMessages = [];

    public function __construct()
    {
        $this->messages = [
            'required' => 'The :field field is required.',
            'email' => 'The :field field must be a valid email address.',
            'min' => 'The :field field must be at least :min characters.',
            'max' => 'The :field field must not exceed :max characters.',
            'confirmed' => 'The :field field confirmation does not match.',
            'unique' => 'The :field has already been taken.',
            'regex' => 'The :field field format is invalid.',
            'numeric' => 'The :field field must be a number.',
            'integer' => 'The :field field must be an integer.',
            'url' => 'The :field field must be a valid URL.',
            'date' => 'The :field field must be a valid date.',
            'boolean' => 'The :field field must be true or false.',
            'array' => 'The :field field must be an array.',
            'in' => 'The selected :field is invalid.',
            'not_in' => 'The selected :field is invalid.',
            'same' => 'The :field and :other must match.',
            'different' => 'The :field and :other must be different.',
            'alpha' => 'The :field field must only contain letters.',
            'alpha_num' => 'The :field field must only contain letters and numbers.',
            'alpha_dash' => 'The :field field must only contain letters, numbers, dashes, and underscores.',
        ];
    }

    public function validate(array $data, array $rules, array $customMessages = []): array
    {
        $this->customMessages = $customMessages;
        $errors = [];

        foreach ($rules as $field => $fieldRules) {
            $fieldRules = is_string($fieldRules) ? explode('|', $fieldRules) : $fieldRules;

            foreach ($fieldRules as $rule) {
                $error = $this->validateRule($field, $data[$field] ?? null, $rule, $data);
                if ($error) {
                    $errors[$field][] = $error;
                }
            }
        }

        return $errors;
    }

    private function validateRule(string $field, $value, string $rule, array $data): ?string
    {
        $ruleParts = explode(':', $rule);
        $ruleName = $ruleParts[0];
        $parameters = isset($ruleParts[1]) ? explode(',', $ruleParts[1]) : [];

        switch ($ruleName) {
            case 'required':
                if (empty($value) && $value !== '0') {
                    return $this->getMessage($field, 'required');
                }
                break;

            case 'email':
                if (!empty($value) && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    return $this->getMessage($field, 'email');
                }
                break;

            case 'min':
                $min = (int) $parameters[0];
                if (!empty($value) && strlen($value) < $min) {
                    return $this->getMessage($field, 'min', ['min' => $min]);
                }
                break;

            case 'max':
                $max = (int) $parameters[0];
                if (!empty($value) && strlen($value) > $max) {
                    return $this->getMessage($field, 'max', ['max' => $max]);
                }
                break;

            case 'confirmed':
                $confirmField = $field . '_confirmation';
                if (!empty($value) && $value !== ($data[$confirmField] ?? null)) {
                    return $this->getMessage($field, 'confirmed');
                }
                break;

            case 'same':
                $otherField = $parameters[0];
                if (!empty($value) && $value !== ($data[$otherField] ?? null)) {
                    return $this->getMessage($field, 'same', ['other' => $otherField]);
                }
                break;

            case 'different':
                $otherField = $parameters[0];
                if (!empty($value) && $value === ($data[$otherField] ?? null)) {
                    return $this->getMessage($field, 'different', ['other' => $otherField]);
                }
                break;

            case 'numeric':
                if (!empty($value) && !is_numeric($value)) {
                    return $this->getMessage($field, 'numeric');
                }
                break;

            case 'integer':
                if (!empty($value) && !filter_var($value, FILTER_VALIDATE_INT)) {
                    return $this->getMessage($field, 'integer');
                }
                break;

            case 'url':
                if (!empty($value) && !filter_var($value, FILTER_VALIDATE_URL)) {
                    return $this->getMessage($field, 'url');
                }
                break;

            case 'regex':
                $pattern = $parameters[0];
                if (!empty($value) && !preg_match($pattern, $value)) {
                    return $this->getMessage($field, 'regex');
                }
                break;

            case 'in':
                if (!empty($value) && !in_array($value, $parameters)) {
                    return $this->getMessage($field, 'in');
                }
                break;

            case 'not_in':
                if (!empty($value) && in_array($value, $parameters)) {
                    return $this->getMessage($field, 'not_in');
                }
                break;

            case 'alpha':
                if (!empty($value) && !preg_match('/^[a-zA-Z]+$/', $value)) {
                    return $this->getMessage($field, 'alpha');
                }
                break;

            case 'alpha_num':
                if (!empty($value) && !preg_match('/^[a-zA-Z0-9]+$/', $value)) {
                    return $this->getMessage($field, 'alpha_num');
                }
                break;

            case 'alpha_dash':
                if (!empty($value) && !preg_match('/^[a-zA-Z0-9_-]+$/', $value)) {
                    return $this->getMessage($field, 'alpha_dash');
                }
                break;

            case 'unique':
                // This should be implemented in the repository layer
                // For now, we'll just return null
                break;
        }

        return null;
    }

    private function getMessage(string $field, string $rule, array $parameters = []): string
    {
        $customKey = "{$field}.{$rule}";

        if (isset($this->customMessages[$customKey])) {
            $message = $this->customMessages[$customKey];
        } elseif (isset($this->customMessages[$rule])) {
            $message = $this->customMessages[$rule];
        } else {
            $message = $this->messages[$rule] ?? "The {$field} field is invalid.";
        }

        $message = str_replace(':field', $field, $message);

        foreach ($parameters as $key => $value) {
            $message = str_replace(":{$key}", $value, $message);
        }

        return $message;
    }
}
