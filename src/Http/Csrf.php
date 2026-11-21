<?php

declare(strict_types=1);

namespace App\Http;

final class Csrf
{
    private const SESSION_KEY = '_csrf_token';

    public static function token(): string
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            throw new \RuntimeException('CSRF token üretmek için oturum başlatılmalıdır.');
        }

        if (!isset($_SESSION[self::SESSION_KEY]) || !is_string($_SESSION[self::SESSION_KEY])) {
            $_SESSION[self::SESSION_KEY] = bin2hex(random_bytes(32));
        }

        return $_SESSION[self::SESSION_KEY];
    }

    public static function validate(?string $token = null): bool
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            return false;
        }

        if ($token === null) {
            $token = isset($_POST['_token']) && is_string($_POST['_token'])
                ? $_POST['_token']
                : null;
        }

        $storedToken = $_SESSION[self::SESSION_KEY] ?? null;

        if (!is_string($token) || $token === '' || !is_string($storedToken) || $storedToken === '') {
            return false;
        }

        return hash_equals($storedToken, $token);
    }
}
