<?php

declare(strict_types=1);

namespace App\Http;

final class Request
{
    /**
     * @param array<string, mixed> $query
     * @param array<string, mixed> $post
     * @param array<string, mixed> $server
     * @param array<string, mixed> $cookies
     */
    public function __construct(
        private array $query = [],
        private array $post = [],
        private array $server = [],
        private array $cookies = []
    ) {
    }

    public static function fromGlobals(): self
    {
        return new self($_GET, $_POST, $_SERVER, $_COOKIE);
    }

    public function method(): string
    {
        return strtoupper((string) ($this->server['REQUEST_METHOD'] ?? 'GET'));
    }

    public function path(): string
    {
        $uri = (string) ($this->server['REQUEST_URI'] ?? '/');
        $path = parse_url($uri, PHP_URL_PATH);

        return is_string($path) && $path !== '' ? $path : '/';
    }

    public function query(string $key, mixed $default = null): mixed
    {
        $value = $this->query[$key] ?? $default;

        return is_scalar($value) || $value === null ? $value : $default;
    }

    public function post(string $key, mixed $default = null): mixed
    {
        $value = $this->post[$key] ?? $default;

        return is_scalar($value) || $value === null ? $value : $default;
    }

    public function cookie(string $key, mixed $default = null): mixed
    {
        $value = $this->cookies[$key] ?? $default;

        return is_scalar($value) || $value === null ? $value : $default;
    }

    public function header(string $name): ?string
    {
        $key = 'HTTP_' . strtoupper(str_replace('-', '_', $name));

        if (isset($this->server[$key])) {
            return (string) $this->server[$key];
        }

        if ($name === 'Content-Type' && isset($this->server['CONTENT_TYPE'])) {
            return (string) $this->server['CONTENT_TYPE'];
        }

        return null;
    }

    public function isPost(): bool
    {
        return $this->method() === 'POST';
    }

    /**
     * @return array<string, mixed>
     */
    public function allPost(): array
    {
        return $this->post;
    }
}
