<?php

namespace Core\Service;

class ValidationService
{
    private static array $messages = [
        'required' => 'The :field field is required.',
        'email' => 'The :field field must be a valid email address.',
        'min' => 'The :field field must be at least :min characters.',
        'max' => 'The :field field must not exceed :max characters.',
        'confirmed' => 'The :field field confirmation does not match.',
        'numeric' => 'The :field field must be a number.',
        'integer' => 'The :field field must be an integer.',
        'url' => 'The :field field must be a valid URL.',
        'alpha' => 'The :field field must only contain letters.',
        'alpha_num' => 'The :field field must only contain letters and numbers.',
        'alpha_dash' => 'The :field field must only contain letters, numbers, dashes, and underscores.',
    ];

    public static function validate(array $data, array $rules): array
    {
        $errors = [];

        foreach ($rules as $field => $fieldRules) {
            $fieldRules = is_string($fieldRules) ? explode('|', $fieldRules) : $fieldRules;
            $value = $data[$field] ?? null;

            foreach ($fieldRules as $rule) {
                $error = self::validateRule($field, $value, $rule, $data);
                if ($error) {
                    $errors[$field] = $error;
                    break; // Stop at first error
                }
            }
        }

        return $errors;
    }

    private static function validateRule(string $field, $value, string $rule, array $data): ?string
    {
        [$ruleName, $parameters] = self::parseRule($rule);

        switch ($ruleName) {
            case 'required':
                return !self::validateRequired($value) ? self::getMessage($field, 'required') : null;

            case 'email':
                return $value && !self::validateEmail($value) ? self::getMessage($field, 'email') : null;

            case 'min':
                $min = (int) $parameters[0];
                return $value && !self::validateMinLength($value, $min) ?
                    str_replace(':min', $min, self::getMessage($field, 'min')) : null;

            case 'max':
                $max = (int) $parameters[0];
                return $value && !self::validateMaxLength($value, $max) ?
                    str_replace(':max', $max, self::getMessage($field, 'max')) : null;

            case 'confirmed':
                return !self::validatePasswordConfirmation($value, $data[$field . '_confirmation'] ?? '') ?
                    self::getMessage($field, 'confirmed') : null;

            case 'numeric':
                return $value && !self::validateNumeric($value) ? self::getMessage($field, 'numeric') : null;

            case 'integer':
                return $value && !self::validateInteger($value) ? self::getMessage($field, 'integer') : null;

            case 'url':
                return $value && !self::validateUrl($value) ? self::getMessage($field, 'url') : null;

            case 'alpha':
                return $value && !self::validateAlpha($value) ? self::getMessage($field, 'alpha') : null;

            case 'alpha_num':
                return $value && !self::validateAlphaNumeric($value) ? self::getMessage($field, 'alpha_num') : null;

            case 'alpha_dash':
                return $value && !self::validateAlphaDash($value) ? self::getMessage($field, 'alpha_dash') : null;
        }

        return null;
    }

    private static function parseRule(string $rule): array
    {
        if (strpos($rule, ':') !== false) {
            [$name, $params] = explode(':', $rule, 2);
            return [$name, explode(',', $params)];
        }

        return [$rule, []];
    }

    private static function getMessage(string $field, string $rule): string
    {
        return str_replace(':field', $field, self::$messages[$rule] ?? 'Validation failed');
    }

    // Original validation methods
    public static function validateEmail(string $email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    public static function validateRequired($value): bool
    {
        if (is_string($value)) {
            return trim($value) !== '';
        }

        if (is_array($value)) {
            return !empty($value);
        }

        return $value !== null;
    }

    public static function validateMinLength(string $value, int $min): bool
    {
        return strlen($value) >= $min;
    }

    public static function validateMaxLength(string $value, int $max): bool
    {
        return strlen($value) <= $max;
    }

    public static function validatePasswordConfirmation(string $password, string $confirmation): bool
    {
        return $password === $confirmation;
    }

    public static function validateUrl(string $url): bool
    {
        return filter_var($url, FILTER_VALIDATE_URL) !== false;
    }

    public static function validateNumeric($value): bool
    {
        return is_numeric($value);
    }

    public static function validateInteger($value): bool
    {
        return filter_var($value, FILTER_VALIDATE_INT) !== false;
    }

    public static function validateBoolean($value): bool
    {
        return is_bool($value) || in_array($value, ['true', 'false', '1', '0', 1, 0], true);
    }

    public static function validateInArray($value, array $allowedValues): bool
    {
        return in_array($value, $allowedValues, true);
    }

    public static function validateRegex(string $value, string $pattern): bool
    {
        return preg_match($pattern, $value) === 1;
    }

    public static function validateAlpha(string $value): bool
    {
        return ctype_alpha($value);
    }

    public static function validateAlphaNumeric(string $value): bool
    {
        return ctype_alnum($value);
    }

    public static function validateAlphaDash(string $value): bool
    {
        return preg_match('/^[a-zA-Z0-9_-]+$/', $value) === 1;
    }
}