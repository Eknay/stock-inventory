<?php

declare(strict_types=1);

use App\Config;

if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(__DIR__));
}

spl_autoload_register(static function (string $class): void {
    $prefix = 'App\\';

    if (!str_starts_with($class, $prefix)) {
        return;
    }

    $relativeClass = substr($class, strlen($prefix));
    $file = BASE_PATH . '/src/' . str_replace('\\', '/', $relativeClass) . '.php';

    if (is_file($file)) {
        require $file;
    }
});

require BASE_PATH . '/src/helpers.php';

$configPath = BASE_PATH . '/config/config.php';
if (!is_file($configPath)) {
    // Docker imajında gerçek config dosyası bilinçli olarak kopyalanmaz.
    // Ortam değişkenleri example dosyasındaki güvenli varsayılanları geçersiz kılar.
    $configPath = BASE_PATH . '/config/config.example.php';
}
$config = Config::load($configPath);
date_default_timezone_set((string) $config['app']['timezone']);

$debug = (bool) $config['app']['debug'];
ini_set('display_errors', $debug ? '1' : '0');
error_reporting(E_ALL);

if (session_status() !== PHP_SESSION_ACTIVE) {
    ini_set('session.use_strict_mode', '1');
    ini_set('session.use_only_cookies', '1');
    session_name((string) $config['session']['name']);

    $isHttps = (!empty($_SERVER['HTTPS']) && strtolower((string) $_SERVER['HTTPS']) !== 'off')
        || (int) ($_SERVER['SERVER_PORT'] ?? 0) === 443;

    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'secure' => $isHttps,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);

    session_start();
}

return $config;
