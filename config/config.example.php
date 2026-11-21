<?php

declare(strict_types=1);

/**
 * Yerel yapılandırma örneği.
 *
 * Bu dosyayı config/config.php olarak kopyalayın. Gerçek config.php dosyası
 * Git tarafından izlenmez ve .gitignore ile dışlanır.
 */

$env = static function (string $key, mixed $default = null): mixed {
    $value = getenv($key);

    return $value === false || $value === '' ? $default : $value;
};

return [
    'app' => [
        'name' => (string) $env('APP_NAME', 'Stok Envanter Takip'),
        'env' => (string) $env('APP_ENV', 'development'),
        'debug' => filter_var($env('APP_DEBUG', '1'), FILTER_VALIDATE_BOOL),
        'timezone' => 'UTC',
    ],
    'database' => [
        'host' => (string) $env('DB_HOST', '127.0.0.1'),
        'port' => (int) $env('DB_PORT', 3306),
        'database' => (string) $env('DB_DATABASE', 'stock_inventory'),
        'username' => (string) $env('DB_USER', 'root'),
        'password' => (string) $env('DB_PASSWORD', ''),
    ],
    'session' => [
        'name' => 'stock_inventory_session',
    ],
];
