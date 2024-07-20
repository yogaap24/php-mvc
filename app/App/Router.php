<?php

namespace Yogaap\PHP\MVC\App;

use Exception;

class Router
{
    private static array $routes = [];
    private static ?string $groupPrefix = '';
    private static ?string $groupController = null;
    private static array $groupMiddlewares = [];
    private static array $groupStack = [];

    public static function group(array $params, callable $callback): void
    {
        $previousGroupPrefix = self::$groupPrefix;
        $previousGroupController = self::$groupController;
        $previousGroupMiddlewares = self::$groupMiddlewares;

        $newPrefix = $params['prefix'] ?? '';
        self::$groupPrefix = rtrim($previousGroupPrefix, '/') . '/' . trim($newPrefix, '/');
        self::$groupController = $params['controller'] ?? self::$groupController;
        self::$groupMiddlewares = $params['middlewares'] ?? self::$groupMiddlewares;

        if (is_callable($callback)) {
            self::$groupStack[] = [
                'prefix' => self::$groupPrefix,
                'controller' => self::$groupController,
                'middlewares' => self::$groupMiddlewares,
            ];
            $callback();
            array_pop(self::$groupStack);
        }

        self::$groupPrefix = $previousGroupPrefix;
        self::$groupController = $previousGroupController;
        self::$groupMiddlewares = $previousGroupMiddlewares;
    }

    public static function add(string $method, string $path, string $controllerOrFunction = '', string $functionOrController = '', array $middlewares = []): void
    {
        $prefix = self::getCurrentGroupPrefix();
        $path = rtrim($prefix, '/') . '/' . ltrim($path, '/');

        $controller = '';
        $function = '';

        if (count(self::$groupStack) > 0) {
            $group = end(self::$groupStack);

            if ($group['controller']) {
                if (is_string($controllerOrFunction) && !class_exists($controllerOrFunction)) {
                    $function = $controllerOrFunction;
                    $controller = $group['controller'];
                } elseif (class_exists($controllerOrFunction)) {
                    $controller = $controllerOrFunction;
                    $function = $functionOrController;
                } else {
                    $controller = $group['controller'];
                    $function = $controllerOrFunction;
                }
            } else {
                $controller = $controllerOrFunction;
                $function = $functionOrController;
            }
        } else {
            $controller = $controllerOrFunction;
            $function = $functionOrController;
        }

        if (is_string($function) && class_exists($function)) {
            $controller = $function;
            $function = '';
        }

        $middlewares = $middlewares ?: self::$groupMiddlewares;

        if (self::routeExists($method, $path, $controller, $function)) {
            throw new Exception("Route {$method} {$path} already exists.");
        }

        self::$routes[] = [
            'method'     => $method,
            'path'       => $path,
            'controller' => $controller,
            'function'   => $function,
            'middleware' => $middlewares
        ];
    }

    private static function getCurrentGroupPrefix(): string
    {
        $prefixes = [];
        foreach (self::$groupStack as $group) {
            if (!empty($group['prefix'])) {
                $prefixes[] = trim($group['prefix'], '/');
            }
        }

        $parts = [];
        foreach ($prefixes as $prefix) {
            $parts = array_merge($parts, explode('/', $prefix));
        }

        $uniqueParts = array_unique(array_filter($parts));
        $combinedPrefix = implode('/', $uniqueParts);

        return $combinedPrefix;
    }

    public static function run(): void
    {
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $method = $_SERVER['REQUEST_METHOD'];

        foreach (self::$routes as $route) {
            if ($method == $route['method']) {

                foreach ($route['middleware'] as $middleware) {
                    $middlewareInstance = new $middleware;
                    $middlewareInstance->before();
                }

                $routePath = trim($route['path'], '/');
                $uriPath = trim($uri, '/');

                $routeSegments = explode('/', $routePath);
                $uriSegments = explode('/', $uriPath);

                if (count($routeSegments) === count($uriSegments)) {
                    $variables = [];
                    $match = true;

                    foreach ($routeSegments as $index => $segment) {
                        if (strpos($segment, '{') === 0 && strpos($segment, '}') === (strlen($segment) - 1)) {
                            $varName = trim($segment, '{}');
                            $variables[$varName] = $uriSegments[$index];
                        } elseif ($segment !== $uriSegments[$index]) {
                            $match = false;
                            break;
                        }
                    }

                    if ($match) {
                        $controllerClass = $route['controller'];
                        $function = $route['function'];
                        if (!empty($function)) {
                            $controller = new $controllerClass;
                            $controller->$function(...array_values($variables));
                            return;
                        }
                    }
                }
            }
        }

        http_response_code(404);
        throw new Exception("Controller or method not found.");
    }

    private static function routeExists(string $method, string $path, string $controller, string $function): bool
    {
        foreach (self::$routes as $route) {
            if ($route['method'] === $method && $route['path'] === $path) {
                if ($route['controller'] === $controller && $route['function'] === $function) {
                    return true;
                }
            }
        }
        return false;
    }
}
