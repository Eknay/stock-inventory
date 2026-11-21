<?php

declare(strict_types=1);

use App\Application;
use App\Http\Request;
use App\Http\SecurityHeaders;

$config = [
    'app' => ['debug' => false],
    'database' => [],
    'session' => [],
];

try {
    $config = require dirname(__DIR__) . '/src/bootstrap.php';
    $request = Request::fromGlobals();

    SecurityHeaders::apply();

    $application = new Application($config);
    $response = $application->run($request);
} catch (Throwable $exception) {
    $viewPath = defined('BASE_PATH') ? BASE_PATH . '/src/Views' : dirname(__DIR__) . '/src/Views';
    $response = Application::errorResponse($exception, $config, $viewPath);
}

$response->send();
