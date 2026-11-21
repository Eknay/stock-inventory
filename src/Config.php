<?php

declare(strict_types=1);

namespace App;

final class Config
{
    /**
     * @return array{app: array<string, mixed>, database: array<string, mixed>, session: array<string, mixed>}
     */
    public static function load(string $path): array
    {
        if (!is_file($path)) {
            throw new \RuntimeException(
                'Yapılandırma dosyası bulunamadı. config/config.example.php dosyasını config/config.php olarak kopyalayın.'
            );
        }

        $config = require $path;

        if (!is_array($config)
            || !isset($config['app'], $config['database'], $config['session'])
            || !is_array($config['app'])
            || !is_array($config['database'])
            || !is_array($config['session'])
        ) {
            throw new \RuntimeException('Yapılandırma dosyası geçersiz.');
        }

        return $config;
    }
}
