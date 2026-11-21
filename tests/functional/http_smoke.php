<?php

declare(strict_types=1);

/**
 * Çalışan uygulama ve veritabanına karşı basit HTTP smoke testi.
 *
 * Kullanım: php tests/functional/http_smoke.php http://localhost:8080
 */

$baseUrl = rtrim($argv[1] ?? 'http://localhost:8080', '/');

/**
 * @return array{status: int, body: string}
 */
function httpGet(string $url): array
{
    $context = stream_context_create([
        'http' => [
            'method' => 'GET',
            'timeout' => 10,
            'ignore_errors' => true,
            'header' => "Accept: application/json,text/html\r\n",
        ],
    ]);

    $body = @file_get_contents($url, false, $context);
    if ($body === false) {
        throw new RuntimeException('İstek başarısız: ' . $url);
    }

    $status = 0;
    foreach ($http_response_header ?? [] as $header) {
        if (preg_match('/^HTTP\/\S+\s+(\d+)/', $header, $matches) === 1) {
            $status = (int) $matches[1];
            break;
        }
    }

    return ['status' => $status, 'body' => $body];
}

$failures = 0;

try {
    $dashboard = httpGet($baseUrl . '/');
    if ($dashboard['status'] !== 200 || !str_contains($dashboard['body'], 'Stok durumu')) {
        throw new RuntimeException('Dashboard smoke testi başarısız. HTTP: ' . $dashboard['status']);
    }
    echo "PASS  GET /" . PHP_EOL;

    $summary = httpGet($baseUrl . '/api/summary');
    $payload = json_decode($summary['body'], true);
    if ($summary['status'] !== 200 || !is_array($payload) || !isset($payload['summary'])) {
        throw new RuntimeException('/api/summary smoke testi başarısız. HTTP: ' . $summary['status']);
    }
    echo "PASS  GET /api/summary" . PHP_EOL;
} catch (Throwable $exception) {
    ++$failures;
    echo "FAIL  " . $exception->getMessage() . PHP_EOL;
}

exit($failures === 0 ? 0 : 1);
