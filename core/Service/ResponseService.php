<?php

namespace Core\Service;

class ResponseService
{
    private $data;
    private $message;
    private $success;

    public function __construct($data = null)
    {
        $this->data = $data;
    }

    public function success($message = null, $responseCode = null)
    {
        $message = (empty($message)) ? 'success' : $message;
        $this->setMessage($message);
        $this->setResponseCode($responseCode);
        $this->success = true;
        return (object) $this->responseWrapper();
    }

    public function error($message = null, $responseCode = null)
    {
        $message = (empty($message)) ? 'error' : $message;
        $this->setMessage($message);
        $this->setResponseCode($responseCode);
        $this->success = false;
        return (object) $this->responseWrapper();
    }

    private function responseWrapper()
    {
        $data = (empty($this->data)) ? null : $this->data;

        $response = [
            'code'      => http_response_code(),
            'success'   => $this->success,
            'message'   => $this->message,
            'data'      => $data,
        ];

        // Check if $data is an array and contains pagination information
        if (is_array($data) && isset($data['items'], $data['total'], $data['perPage'], $data['currentPage'])) {
            $paginationData = [
                'total'         => $data['total'],
                'perPage'       => $data['perPage'],
                'currentPage'   => $data['currentPage'],
                'from'          => $data['from'],
                'to'            => $data['to'],
                'lastPage'      => $data['lastPage'],
                'nextPageUrl'   => $data['nextPageUrl'],
                'prevPageUrl'   => $data['prevPageUrl'],
                'path'          => $data['path'],
            ];

            $response['pagination'] = $paginationData;
            $response['data'] = $data['items'];
        }

        return $response;
    }

    private function setMessage($message)
    {
        if (is_array($message)) {
            $this->message = $message[0];
        } else {
            $this->message = $message;
        }
    }

    private function setResponseCode($responseCode)
    {
        if (!empty($responseCode) && is_numeric($responseCode)) {
            http_response_code($responseCode);
        }
    }
}