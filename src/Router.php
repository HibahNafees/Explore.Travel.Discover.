<?php

declare(strict_types=1);

namespace App;

/**
 * Tiny router: "GET /destinations/{slug}" => callable.
 * Every POST request must carry a valid CSRF token, checked here centrally.
 */
final class Router
{
    /** @var list<array{string,string,callable}> */
    private array $routes = [];

    public function get(string $pattern, callable $handler): void
    {
        $this->routes[] = ['GET', $pattern, $handler];
    }

    public function post(string $pattern, callable $handler): void
    {
        $this->routes[] = ['POST', $pattern, $handler];
    }

    public function dispatch(string $method, string $uri): void
    {
        $path = rawurldecode(parse_url($uri, PHP_URL_PATH) ?: '/');
        if ($path !== '/' && str_ends_with($path, '/')) {
            $path = rtrim($path, '/');
        }
        $method = strtoupper($method) === 'HEAD' ? 'GET' : strtoupper($method);

        $allowed = [];
        foreach ($this->routes as [$routeMethod, $pattern, $handler]) {
            $regex = '#^' . preg_replace('#\{(\w+)\}#', '(?P<$1>[A-Za-z0-9_-]+)', $pattern) . '$#';
            if (!preg_match($regex, $path, $matches)) {
                continue;
            }
            if ($routeMethod !== $method) {
                $allowed[] = $routeMethod;
                continue;
            }
            if ($method === 'POST' && !Csrf::valid($_POST['_token'] ?? null)) {
                throw new HttpException(419, 'Your session expired or the form was invalid. Please go back, refresh the page and try again.');
            }
            $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
            $handler(...$params);
            return;
        }

        if ($allowed) {
            header('Allow: ' . implode(', ', array_unique($allowed)));
            throw new HttpException(405, 'Method not allowed.');
        }
        throw new HttpException(404, 'Page not found.');
    }
}
