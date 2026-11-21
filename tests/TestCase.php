<?php

declare(strict_types=1);

namespace App\Tests;

abstract class TestCase
{
    private int $assertionCount = 0;

    public function run(): int
    {
        $failures = 0;
        $methods = get_class_methods($this);

        foreach ($methods as $method) {
            if (!str_starts_with($method, 'test')) {
                continue;
            }

            try {
                $this->{$method}();
                echo "PASS  " . static::class . "::" . $method . PHP_EOL;
            } catch (\Throwable $exception) {
                ++$failures;
                echo "FAIL  " . static::class . "::" . $method . PHP_EOL;
                echo "      " . $exception->getMessage() . PHP_EOL;
            }
        }

        return $failures;
    }

    public function assertionCount(): int
    {
        return $this->assertionCount;
    }

    protected function assertTrue(bool $condition, string $message = 'Beklenen koşul sağlanmadı.'): void
    {
        ++$this->assertionCount;

        if (!$condition) {
            throw new \RuntimeException($message);
        }
    }

    protected function assertFalse(bool $condition, string $message = 'Beklenmeyen koşul sağlandı.'): void
    {
        $this->assertTrue(!$condition, $message);
    }

    /**
     * @param mixed $expected
     * @param mixed $actual
     */
    protected function assertSame(mixed $expected, mixed $actual, string $message = ''): void
    {
        ++$this->assertionCount;

        if ($expected !== $actual) {
            throw new \RuntimeException(
                $message !== '' ? $message : sprintf(
                    'Değerler eşleşmedi. Beklenen: %s, gerçek: %s',
                    var_export($expected, true),
                    var_export($actual, true)
                )
            );
        }
    }

    protected function assertArrayHasKey(string $key, array $array, string $message = ''): void
    {
        ++$this->assertionCount;

        if (!array_key_exists($key, $array)) {
            throw new \RuntimeException($message !== '' ? $message : 'Beklenen dizi anahtarı bulunamadı: ' . $key);
        }
    }

    protected function assertCount(int $expected, array $array, string $message = ''): void
    {
        $this->assertSame($expected, count($array), $message);
    }
}
