<?php

namespace Yogaap\PHP\MVC\Middleware;

use Yogaap\PHP\MVC\Helper\CSRFToken;
use Yogaap\PHP\MVC\Helper\ValidationException;

class CSRFMiddleware implements Middleware
{
    private array $excludedMethods = ['GET', 'HEAD', 'OPTIONS'];

    public function before(): void
    {
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

        if (in_array($method, $this->excludedMethods)) {
            return;
        }

        $tokenName = CSRFToken::getTokenName();
        $token = $_POST[$tokenName] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? null;

        if (!CSRFToken::validate($token)) {
            http_response_code(403);
            throw new ValidationException('CSRF token mismatch. Please refresh the page and try again.');
        }
    }
}
