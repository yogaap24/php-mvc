<?php

namespace App\Exception;

use Core\Http\Response;
use Core\Support\FlashMessage;

class Handler
{
    public function report(\Throwable $exception): void
    {
        // Log the exception (implement logging if needed)
        error_log($exception->getMessage() . ' in ' . $exception->getFile() . ':' . $exception->getLine());
    }

    public function render(\Throwable $exception): Response
    {
        $isDevelopment = $_ENV['APP_ENV'] === 'development' || $_ENV['APP_DEBUG'] === 'true';

        if ($isDevelopment) {
            return $this->renderDevelopmentError($exception);
        }

        return $this->renderProductionError($exception);
    }

    private function renderDevelopmentError(\Throwable $exception): Response
    {
        $data = [
            'error' => $exception->getMessage(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'trace' => $exception->getTraceAsString(),
            'type' => get_class($exception)
        ];

        if ($exception instanceof AppException) {
            $data['context'] = $exception->getContext();
        }

        return new Response($data, 500);
    }

    private function renderProductionError(\Throwable $exception): Response
    {
        $statusCode = $this->getStatusCode($exception);

        $data = [
            'error' => $this->getPublicMessage($statusCode),
            'code' => $statusCode
        ];

        return new Response($data, $statusCode);
    }

    private function getStatusCode(\Throwable $exception): int
    {
        // Check if exception has a status code property/method
        if (property_exists($exception, 'statusCode')) {
            return $exception->statusCode;
        }

        return $exception->getCode() ?: 500;
    }

    private function getPublicMessage(int $statusCode): string
    {
        $messages = [
            400 => 'Bad Request',
            401 => 'Unauthorized',
            403 => 'Forbidden',
            404 => 'Not Found',
            419 => 'CSRF Token Mismatch',
            422 => 'Unprocessable Entity',
            500 => 'Internal Server Error',
        ];

        return $messages[$statusCode] ?? 'An error occurred';
    }
}
