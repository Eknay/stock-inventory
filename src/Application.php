<?php

declare(strict_types=1);

namespace App;

use App\Controller\CategoryController;
use App\Controller\DashboardController;
use App\Controller\ProductController;
use App\Exception\HttpException;
use App\Http\Csrf;
use App\Http\Request;
use App\Http\Response;
use App\Http\Router;
use App\Repository\CategoryRepository;
use App\Repository\DashboardRepository;
use App\Repository\ProductRepository;
use App\Repository\StockMovementRepository;
use App\Service\CategoryService;
use App\Service\ProductService;
use App\Service\StockService;
use App\Validation\CategoryValidator;
use App\Validation\ProductValidator;

final class Application
{
    private Router $router;

    /**
     * @param array<string, mixed> $config
     */
    public function __construct(private array $config)
    {
        $database = new Database($config['database']);
        $pdo = $database->pdo();

        $categoryRepository = new CategoryRepository($pdo);
        $productRepository = new ProductRepository($pdo);
        $movementRepository = new StockMovementRepository($pdo);
        $dashboardRepository = new DashboardRepository($pdo);

        $categoryService = new CategoryService($categoryRepository, new CategoryValidator());
        $productService = new ProductService(
            $database,
            $productRepository,
            $categoryRepository,
            $movementRepository,
            new ProductValidator()
        );
        $stockService = new StockService($database, $productRepository, $movementRepository);

        $view = new View(BASE_PATH . '/src/Views', (string) ($config['app']['name'] ?? 'Stok Envanter Takip'));
        $dashboardController = new DashboardController($dashboardRepository, $movementRepository, $view);
        $productController = new ProductController(
            $productRepository,
            $categoryRepository,
            $productService,
            $stockService,
            $movementRepository,
            $view
        );
        $categoryController = new CategoryController($categoryRepository, $categoryService, $view);

        $this->router = new Router();
        $this->router->get('/', [$dashboardController, 'index']);
        $this->router->get('/api/summary', [$dashboardController, 'summary']);
        $this->router->get('/products', [$productController, 'index']);
        $this->router->get('/products/create', [$productController, 'create']);
        $this->router->post('/products', [$productController, 'store']);
        $this->router->get('/products/(\d+)/edit', [$productController, 'edit']);
        $this->router->post('/products/(\d+)', [$productController, 'update']);
        $this->router->get('/products/(\d+)/stock', [$productController, 'stock']);
        $this->router->post('/products/(\d+)/stock', [$productController, 'storeMovement']);
        $this->router->get('/categories', [$categoryController, 'index']);
        $this->router->post('/categories', [$categoryController, 'store']);
    }

    public function run(Request $request): Response
    {
        if ($request->isPost()) {
            $token = $request->post('_token');
            if (!is_string($token)) {
                $token = $request->header('X-CSRF-Token');
            }

            if (!Csrf::validate(is_string($token) ? $token : null)) {
                throw new HttpException(419, 'Oturum doğrulaması başarısız oldu. Lütfen formu yeniden gönderin.');
            }
        }

        return $this->router->dispatch($request);
    }

    /**
     * @param array<string, mixed> $config
     */
    public static function errorResponse(
        \Throwable $exception,
        array $config,
        string $viewPath
    ): Response {
        $statusCode = $exception instanceof HttpException ? $exception->statusCode() : 500;
        $debug = (bool) ($config['app']['debug'] ?? false);

        if ($statusCode >= 500) {
            $message = sprintf(
                "[%s] %s in %s:%d\n%s\n",
                date('c'),
                $exception->getMessage(),
                $exception->getFile(),
                $exception->getLine(),
                $exception->getTraceAsString()
            );
            error_log($message);
        }

        $view = new View($viewPath);

        return $view->render('error', [
            'statusCode' => $statusCode,
            'message' => $statusCode >= 500 && !$debug
                ? 'Beklenmeyen bir hata oluştu. Lütfen daha sonra tekrar deneyin.'
                : $exception->getMessage(),
            'debug' => $debug && $statusCode >= 500,
            'details' => $debug && $statusCode >= 500 ? $exception->getTraceAsString() : '',
        ], 'Hata', $statusCode);
    }
}
