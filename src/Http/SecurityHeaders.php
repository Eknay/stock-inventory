<?php

declare(strict_types=1);

namespace App\Http;

final class SecurityHeaders
{
    public static function apply(): void
    {
        if (headers_sent()) {
            return;
        }

        header('X-Content-Type-Options: nosniff');
        header('X-Frame-Options: DENY');
        header('Referrer-Policy: strict-origin-when-cross-origin');
        header('Permissions-Policy: camera=(), microphone=(), geolocation=()');
        header(
            "Content-Security-Policy: default-src 'self'; "
            . "style-src 'self'; script-src 'self'; img-src 'self' data:; "
            . "form-action 'self'; frame-ancestors 'none'; base-uri 'self'"
        );
    }
}
