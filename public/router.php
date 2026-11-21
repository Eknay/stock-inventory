<?php

declare(strict_types=1);

/**
 * PHP yerleşik sunucusu için statik dosya ve front-controller yönlendirmesi.
 */

$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$publicRoot = realpath(__DIR__);
$requestedFile = realpath(__DIR__ . '/' . ltrim($path, '/'));
$isInsidePublic = $publicRoot !== false
    && $requestedFile !== false
    && str_starts_with($requestedFile, $publicRoot . DIRECTORY_SEPARATOR);

if ($path !== '/' && $isInsidePublic && is_file($requestedFile) && !str_ends_with(strtolower($requestedFile), '.php')) {
    return false;
}

require __DIR__ . '/index.php';
