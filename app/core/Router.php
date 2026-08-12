<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Router
 *
 * Lightweight, robust HTTP router supporting GET, POST, PUT, DELETE routes,
 * dynamic parameters (e.g. /students/{id}), middleware callbacks, and controller dispatch.
 */
class Router
{
    private static array $routes = [];

    /**
     * Register a GET route.
     */
    public static function get(string $path, array|callable $handler, array $middleware = []): void
    {
        self::add('GET', $path, $handler, $middleware);
    }

    /**
     * Register a POST route.
     */
    public static function post(string $path, array|callable $handler, array $middleware = []): void
    {
        self::add('POST', $path, $handler, $middleware);
    }

    /**
     * Register a route for specified methods.
     */
    public static function add(string $method, string $path, array|callable $handler, array $middleware = []): void
    {
        $path = '/' . trim($path, '/');
        self::$routes[] = [
            'method'     => strtoupper($method),
            'path'       => $path,
            'handler'    => $handler,
            'middleware' => $middleware,
        ];
    }

    /**
     * Dispatch the current HTTP request.
     */
    public static function dispatch(): void
    {
        $requestMethod = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $requestUri    = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);

        // Normalize base path if project is in a subdirectory (e.g. /CMS/public)
        $scriptName = dirname($_SERVER['SCRIPT_NAME']);
        if ($scriptName !== '/' && $scriptName !== '\\' && str_starts_with($requestUri, $scriptName)) {
            $requestUri = substr($requestUri, strlen($scriptName));
        }

        $requestUri = '/' . trim($requestUri, '/');

        foreach (self::$routes as $route) {
            if ($route['method'] !== $requestMethod) {
                continue;
            }

            // Convert route pattern {param} to regex
            $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '(?P<$1>[^/]+)', $route['path']);
            $pattern = '#^' . $pattern . '$#';

            if (preg_match($pattern, $requestUri, $matches)) {
                // Extract named parameters
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);

                // Run middlewares
                foreach ($route['middleware'] as $mw) {
                    if (is_callable($mw)) {
                        $res = call_user_func($mw);
                        if ($res === false) {
                            return;
                        }
                    }
                }

                // Handle callback or controller array [ControllerClass, 'methodName']
                $handler = $route['handler'];

                $positionalParams = array_values($params);

                if (is_callable($handler)) {
                    call_user_func_array($handler, $positionalParams);
                    return;
                }

                if (is_array($handler) && count($handler) === 2) {
                    [$controllerClass, $method] = $handler;

                    if (!class_exists($controllerClass)) {
                        http_response_code(500);
                        die("[CMS Router] Controller class not found: {$controllerClass}");
                    }

                    $controller = new $controllerClass();

                    if (!method_exists($controller, $method)) {
                        http_response_code(500);
                        die("[CMS Router] Method {$method} not found in {$controllerClass}");
                    }

                    call_user_func_array([$controller, $method], $positionalParams);
                    return;
                }
            }
        }

        // 404 Not Found if no route matches
        http_response_code(404);
        if (file_exists(APP_PATH . '/modules/Master/views/404.php')) {
            require APP_PATH . '/modules/Master/views/404.php';
        } else {
            echo "404 - Page Not Found";
        }
    }
}
