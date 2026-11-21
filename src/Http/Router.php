<?php

declare(strict_types=1);

namespace App\Http;

use App\Exception\HttpException;

final class Router
{
    /**
     * @var list<array{method: string, pattern: string, handler: callable}>
     */
    private array $routes = [];

    public function get(string $pattern, callable $handler): void
    {
        $this->add('GET', $pattern, $handler);
    }

    public function post(string $pattern, callable $handler): void
    {
        $this->add('POST', $pattern, $handler);
    }

    public function add(string $method, string $pattern, callable $handler): void
    {
        $this->routes[] = [
            'method' => strtoupper($method),
            'pattern' => $pattern,
            'handler' => $handler,
        ];
    }

    public function dispatch(Request $request): Response
    {
        $path = rtrim($request->path(), '/');
        $path = $path === '' ? '/' : $path;
        $allowedMethods = [];

        foreach ($this->routes as $route) {
            if (!preg_match('#^' . $route['pattern'] . '$#', $path, $matches)) {
                continue;
            }

            if ($route['method'] !== $request->method()) {
                $allowedMethods[] = $route['method'];
                continue;
            }

            array_shift($matches);
            $params = array_values(array_map('strval', $matches));

            /** @var Response $response */
            $response = ($route['handler'])($request, $params);

            return $response;
        }

        if ($allowedMethods !== []) {
            throw new HttpException(405, 'Bu adres için istenen HTTP yöntemi desteklenmiyor.');
        }

        throw new HttpException(404, 'İstenen sayfa bulunamadı.');
    }
}
