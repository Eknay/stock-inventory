<?php

declare(strict_types=1);

namespace App\Tests\Unit;

use App\Exception\HttpException;
use App\Http\Request;
use App\Http\Response;
use App\Http\Router;
use App\Tests\TestCase;

final class RouterTest extends TestCase
{
    public function testDynamicRouteReturnsCapturedParameter(): void
    {
        $router = new Router();
        $router->get('/products/(\d+)/edit', static function (Request $request, array $params): Response {
            return Response::json(['id' => (int) $params[0]]);
        });

        $request = new Request([], [], [
            'REQUEST_METHOD' => 'GET',
            'REQUEST_URI' => '/products/42/edit',
        ]);
        $response = $router->dispatch($request);
        $data = json_decode($response->body(), true);

        $this->assertSame(200, $response->statusCode());
        $this->assertSame(42, $data['id'] ?? null);
    }

    public function testUnknownRouteRaisesHttpException(): void
    {
        $router = new Router();
        $request = new Request([], [], [
            'REQUEST_METHOD' => 'GET',
            'REQUEST_URI' => '/bilinmeyen',
        ]);

        $caught = false;
        try {
            $router->dispatch($request);
        } catch (HttpException $exception) {
            $caught = $exception->statusCode() === 404;
        }

        $this->assertTrue($caught, 'Bilinmeyen adres 404 hatası üretmeli.');
    }
}
