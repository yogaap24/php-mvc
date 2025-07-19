<?php

namespace Core\Http;

class Request
{
    private array $query;
    private array $post;
    private array $server;
    private array $cookies;
    private array $files;

    public function __construct(array $query = [], array $post = [], array $server = [], array $cookies = [], array $files = [])
    {
        $this->query = $query;
        $this->post = $post;
        $this->server = $server;
        $this->cookies = $cookies;
        $this->files = $files;
    }

    public static function createFromGlobals(): self
    {
        return new self($_GET, $_POST, $_SERVER, $_COOKIE, $_FILES);
    }

    public function getMethod(): string
    {
        return $this->server['REQUEST_METHOD'] ?? 'GET';
    }

    public function getPath(): string
    {
        $path = $this->server['REQUEST_URI'] ?? '/';

        // Remove query string
        if (($pos = strpos($path, '?')) !== false) {
            $path = substr($path, 0, $pos);
        }

        return $path;
    }

    public function getQuery(string $key = null, $default = null)
    {
        if ($key === null) {
            return $this->query;
        }

        return $this->query[$key] ?? $default;
    }

    public function getPost(string $key = null, $default = null)
    {
        if ($key === null) {
            return $this->post;
        }

        return $this->post[$key] ?? $default;
    }

    public function getServer(string $key = null, $default = null)
    {
        if ($key === null) {
            return $this->server;
        }

        return $this->server[$key] ?? $default;
    }

    public function getCookie(string $key = null, $default = null)
    {
        if ($key === null) {
            return $this->cookies;
        }

        return $this->cookies[$key] ?? $default;
    }

    public function getFile(string $key = null)
    {
        if ($key === null) {
            return $this->files;
        }

        return $this->files[$key] ?? null;
    }

    public function isApiRequest(): bool
    {
        // Check if path starts with /api/
        if (strpos($this->getPath(), '/api/') === 0) {
            return true;
        }

        // Check Accept header for JSON
        $acceptHeader = $this->getServer('HTTP_ACCEPT', '');
        if (strpos($acceptHeader, 'application/json') !== false) {
            return true;
        }

        // Check for AJAX requests
        if ($this->getServer('HTTP_X_REQUESTED_WITH') === 'XMLHttpRequest') {
            return true;
        }

        // Check for specific API headers
        if ($this->getServer('HTTP_CONTENT_TYPE') === 'application/json') {
            return true;
        }

        return false;
    }

    public function expectsJson(): bool
    {
        return $this->isApiRequest();
    }

    public function wantsJson(): bool
    {
        return $this->isApiRequest();
    }

    public function isPost(): bool
    {
        return $this->getMethod() === 'POST';
    }

    public function isGet(): bool
    {
        return $this->getMethod() === 'GET';
    }

    public function isAjax(): bool
    {
        return strtolower($this->getServer('HTTP_X_REQUESTED_WITH', '')) === 'xmlhttprequest';
    }
}