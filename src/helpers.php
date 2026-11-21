<?php

declare(strict_types=1);

use App\Http\Csrf;

if (!function_exists('e')) {
    /**
     * HTML çıktısını güvenli biçimde escape eder.
     */
    function e(mixed $value): string
    {
        return htmlspecialchars(
            (string) ($value ?? ''),
            ENT_QUOTES | ENT_SUBSTITUTE,
            'UTF-8'
        );
    }
}

if (!function_exists('csrf_field')) {
    function csrf_field(): string
    {
        return '<input type="hidden" name="_token" value="' . e(Csrf::token()) . '">';
    }
}

if (!function_exists('set_flash')) {
    function set_flash(string $type, string $message): void
    {
        if (!isset($_SESSION['_flash']) || !is_array($_SESSION['_flash'])) {
            $_SESSION['_flash'] = [];
        }

        $_SESSION['_flash'][] = [
            'type' => $type,
            'message' => $message,
        ];
    }
}

if (!function_exists('pull_flashes')) {
    /**
     * @return list<array{type: string, message: string}>
     */
    function pull_flashes(): array
    {
        $flashes = $_SESSION['_flash'] ?? [];
        unset($_SESSION['_flash']);

        return is_array($flashes) ? $flashes : [];
    }
}

if (!function_exists('text_length')) {
    /**
     * Multibyte eklentisine ihtiyaç duymadan karakter sayısını döndürür.
     */
    function text_length(string $value): int
    {
        $length = preg_match_all('/./us', $value);

        return $length === false ? strlen($value) : $length;
    }
}

if (!function_exists('format_number')) {
    function format_number(int|float|string $value): string
    {
        return number_format((float) $value, 0, ',', '.');
    }
}
