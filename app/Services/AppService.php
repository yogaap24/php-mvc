<?php

namespace Yogaap\PHP\MVC\Services;

class AppService
{
    /**
     * Send Response Success
     *
     * @param  array|object $data
     * @param  string|array $message
     * @param  int $statusCode
     * @return  object
     */
    protected function sendSuccess($data = null, $message = null, $statusCode = null)
    {
        return (new ResponseService($data))->success($message, $statusCode);
    }

    /**
     * Send Response Error
     *
     * @param  array|object $data
     * @param  string|array $message
     * @param  int $statusCode
     * @return  object
     */
    protected function sendError($data = null, $message = null, $statusCode = null)
    {
        return (new ResponseService($data))->error($message, $statusCode);
    }
}