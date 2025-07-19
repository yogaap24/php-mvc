<?php

namespace Core\Foundation;

use Core\Http\Router;
use Core\Http\Request;
use Core\Http\Response;

class Application
{
    private Container $container;
    private array $providers = [];
    private array $middleware = [];
    private bool $booted = false;

    public function __construct()
    {
        $this->container = new Container();
        $this->registerCoreServices();
    }

    public function registerProvider($provider): void
    {
        $this->providers[] = $provider;
        $provider->register($this->container);
    }

    public function addMiddleware(string $middleware): void
    {
        $this->middleware[] = $middleware;
    }

    public function run(): void
    {
        $this->bootProviders();

        $request = Request::createFromGlobals();
        $response = $this->handleRequest($request);

        $response->send();
    }

    private function handleRequest(Request $request): Response
    {
        try {
            return Router::dispatch($request);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    private function registerCoreServices(): void
    {
        $this->container->instance('app', $this);
        $this->container->bind('request', fn() => Request::createFromGlobals());
    }

    private function bootProviders(): void
    {
        if ($this->booted) {
            return;
        }

        foreach ($this->providers as $provider) {
            $provider->boot();
        }

        $this->booted = true;
    }

    private function handleException(\Exception $e): Response
    {
        $data = [
            'error' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine()
        ];

        return new Response($data, 500);
    }

    public function getContainer(): Container
    {
        return $this->container;
    }
}