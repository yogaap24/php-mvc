<?php

namespace Core\Http;

use Core\Foundation\Container;

class Router
{
    private static array $routes = [];
    private static array $middleware = [];
    private static array $currentMiddleware = [];
    private static ?string $currentPrefix = null;
    private static ?Container $container = null;

    public static function setContainer(Container $container): void
    {
        self::$container = $container;
    }

    public static function get(string $path, $handler): void
    {
        self::addRoute('GET', $path, $handler);
    }

    public static function post(string $path, $handler): void
    {
        self::addRoute('POST', $path, $handler);
    }

    public static function put(string $path, $handler): void
    {
        self::addRoute('PUT', $path, $handler);
    }

    public static function delete(string $path, $handler): void
    {
        self::addRoute('DELETE', $path, $handler);
    }

    public static function group(array $options, \Closure $callback): void
    {
        $previousMiddleware = self::$currentMiddleware;
        $previousPrefix = self::$currentPrefix;

        if (isset($options['middleware'])) {
            self::$currentMiddleware = array_merge(self::$currentMiddleware, (array)$options['middleware']);
        }

        if (isset($options['prefix'])) {
            self::$currentPrefix = (self::$currentPrefix ?? '') . '/' . trim($options['prefix'], '/');
        }

        $callback();

        self::$currentMiddleware = $previousMiddleware;
        self::$currentPrefix = $previousPrefix;
    }

    public static function resource(string $name, string $controller): void
    {
        $name = trim($name, '/');

        self::get("/{$name}", "{$controller}@index");
        self::post("/{$name}", "{$controller}@store");
        self::get("/{$name}/{{$name}}", "{$controller}@show");
        self::put("/{$name}/{{$name}}", "{$controller}@update");
        self::delete("/{$name}/{{$name}}", "{$controller}@destroy");
    }

    public static function api(string $version = 'v1'): void
    {
        self::$currentPrefix = "/api/{$version}";
        self::$currentMiddleware = ['Core\Middleware\CorsMiddleware'];
    }

    private static function addRoute(string $method, string $path, $handler): void
    {
        $fullPath = (self::$currentPrefix ?? '') . '/' . trim($path, '/');
        $fullPath = '/' . trim($fullPath, '/');

        self::$routes[] = [
            'method' => $method,
            'path' => $fullPath,
            'handler' => $handler,
            'middleware' => self::$currentMiddleware
        ];
    }

    public static function dispatch(Request $request): Response
    {
        $method = $request->getMethod();
        $path = $request->getPath();

        foreach (self::$routes as $route) {
            if ($route['method'] === $method && self::matchPath($route['path'], $path)) {
                return self::executeRoute($route, $request);
            }
        }

        throw new \Exception("Route not found: {$method} {$path}", 404);
    }

    private static function matchPath(string $routePath, string $requestPath): bool
    {
        // Convert route pattern to regex for parameter matching
        $pattern = preg_replace('/\{([^}]+)\}/', '([^/]+)', $routePath);
        $pattern = '#^' . $pattern . '$#';

        return preg_match($pattern, $requestPath);
    }

    private static function executeRoute(array $route, Request $request): Response
    {
        // Execute middleware
        foreach ($route['middleware'] as $middleware) {
            if (self::$container) {
                $middlewareInstance = self::$container->make($middleware);
                $response = $middlewareInstance->handle($request, function($req) use ($route) {
                    return new Response('', 200); // Placeholder
                });

                if ($response->getStatusCode() !== 200) {
                    return $response;
                }
            }
        }

        // Execute handler
        if (is_string($route['handler'])) {
            if (strpos($route['handler'], '@') !== false) {
                list($class, $method) = explode('@', $route['handler']);
                if (self::$container) {
                    $controller = self::$container->make($class);
                    return $controller->$method($request);
                }
            }
        }

        if (is_callable($route['handler'])) {
            return $route['handler']($request);
        }

        throw new \Exception("Invalid route handler");
    }

    public static function getRoutes(): array
    {
        return self::$routes;
    }

    public static function clearRoutes(): void
    {
        self::$routes = [];
        self::$middleware = [];
        self::$currentMiddleware = [];
        self::$currentPrefix = null;
    }
}