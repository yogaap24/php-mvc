<?php

namespace Core\Service;

interface ServiceInterface
{
    /**
     * Send successful response
     */
    public function sendSuccess($data = null, $message = null, $statusCode = null);

    /**
     * Send error response
     */
    public function sendError($data = null, $message = null, $statusCode = null);
}