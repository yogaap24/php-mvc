<?php

namespace Core\Service;

use Core\Service\ResponseService;

abstract class BaseService implements ServiceInterface
{
    /**
     * Send Response Success
     *
     * @param  array|object $data
     * @param  string|array $message
     * @param  int $statusCode
     */
    public function sendSuccess($data = null, $message = null, $statusCode = null)
    {
        return (new ResponseService($data))->success($message, $statusCode);
    }

    /**
     * Send Response Error
     *
     * @param  array|object $data
     * @param  string|array $message
     * @param  int $statusCode
     */
    public function sendError($data = null, $message = null, $statusCode = null)
    {
        return (new ResponseService($data))->error($message, $statusCode);
    }
}