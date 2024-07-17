<?php

namespace Yogaap\PHP\MVC\App;

class Router
{
    private static array $routes = [];

    public static function add(string $method, string $path, string $controller, string $function, array $middlewares = []): void
    {
        self::$routes[] = [
            'method'     => $method,
            'path'       => $path,
            'controller' => $controller,
            'function'   => $function,
            'middleware' => $middlewares
        ];
    }

    public static function run(): void
    {
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $method = $_SERVER['REQUEST_METHOD'];

        foreach (self::$routes as $route) {
            if ($method == $route['method']) {

                // Check middleware
                foreach ($route['middleware'] as $middleware) {
                    $middlewareInstance = new $middleware;
                    $middlewareInstance->before();
                }

                // Check if the route matches the current request
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
                        $controller = new $route['controller'];
                        $function = $route['function'];
                        $controller->$function(...array_values($variables));
                        return;
                    }
                }
            }
        }

        http_response_code(404);
        echo "Controller Not Found";
    }
}
