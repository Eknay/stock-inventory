<?php

declare(strict_types=1);

namespace App\Exception;

final class HttpException extends \RuntimeException
{
    public function __construct(
        private int $statusCode,
        string $message = '',
        ?\Throwable $previous = null
    ) {
        parent::__construct($message !== '' ? $message : 'HTTP isteği tamamlanamadı.', 0, $previous);
    }

    public function statusCode(): int
    {
        return $this->statusCode;
    }
}
