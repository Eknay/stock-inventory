<?php

declare(strict_types=1);

namespace App\Tests\Unit;

use App\Http\Csrf;
use App\Tests\TestCase;

final class CsrfTest extends TestCase
{
    public function testTokenIsStableAndValidated(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        $_SESSION = [];
        $token = Csrf::token();

        $this->assertTrue(strlen($token) >= 32, 'CSRF token yeterince uzun olmalı.');
        $this->assertSame($token, Csrf::token(), 'Aynı oturumda token değişmemeli.');
        $this->assertTrue(Csrf::validate($token), 'Geçerli token kabul edilmeli.');
    }

    public function testInvalidTokenIsRejected(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        $_SESSION = [];
        Csrf::token();

        $this->assertFalse(Csrf::validate('yanlış-token'), 'Geçersiz token reddedilmeli.');
        $this->assertFalse(Csrf::validate(''), 'Boş token reddedilmeli.');
    }
}
