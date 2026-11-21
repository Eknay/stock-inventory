<?php

declare(strict_types=1);

namespace App\Controller;

use App\Exception\HttpException;
use App\Exception\ValidationException;
use App\Http\Request;
use App\Http\Response;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use App\Repository\StockMovementRepository;
use App\Service\ProductService;
use App\Service\StockService;
use App\View;
use DomainException;

final class ProductController
{
    public function __construct(
        private ProductRepository $products,
        private CategoryRepository $categories,
        private ProductService $productService,
        private StockService $stockService,
        private StockMovementRepository $movements,
        private View $view
    ) {
    }

    public function index(Request $request, array $params = []): Response
    {
        $filters = [
            'search' => trim((string) $request->query('search', '')),
            'category_id' => $request->query('category_id'),
            'status' => (string) $request->query('status', 'all'),
        ];

        if (!in_array($filters['status'], ['all', 'available', 'critical', 'out'], true)) {
            $filters['status'] = 'all';
        }

        return $this->view->render('products/index', [
            'page' => 'products',
            'products' => $this->products->all($filters),
            'categories' => $this->categories->all(),
            'filters' => $filters,
        ], 'Ürünler');
    }

    public function create(Request $request, array $params = []): Response
    {
        return $this->view->render('products/form', [
            'page' => 'products',
            'mode' => 'create',
            'product' => [
                'name' => '',
                'sku' => '',
                'category_id' => '',
                'unit' => 'adet',
                'critical_threshold' => 5,
                'quantity' => 0,
                'description' => '',
            ],
            'categories' => $this->categories->all(),
            'errors' => [],
        ], 'Yeni Ürün');
    }

    public function store(Request $request, array $params = []): Response
    {
        $input = $this->productInput($request);

        try {
            $this->productService->create($input);
            set_flash('success', 'Ürün başarıyla eklendi.');
            return Response::redirect('/products');
        } catch (ValidationException $exception) {
            return $this->view->render('products/form', [
                'page' => 'products',
                'mode' => 'create',
                'product' => $input,
                'categories' => $this->categories->all(),
                'errors' => $exception->errors(),
            ], 'Yeni Ürün', 422);
        }
    }

    public function edit(Request $request, array $params): Response
    {
        $product = $this->products->findById((int) ($params[0] ?? 0));
        if ($product === null) {
            throw new HttpException(404, 'Ürün bulunamadı.');
        }

        return $this->view->render('products/form', [
            'page' => 'products',
            'mode' => 'edit',
            'product' => $product,
            'categories' => $this->categories->all(),
            'errors' => [],
        ], 'Ürünü Düzenle');
    }

    public function update(Request $request, array $params): Response
    {
        $id = (int) ($params[0] ?? 0);
        $product = $this->products->findById($id);
        if ($product === null) {
            throw new HttpException(404, 'Ürün bulunamadı.');
        }

        $input = $this->productInput($request);

        try {
            $this->productService->update($id, $input);
            set_flash('success', 'Ürün bilgileri güncellendi.');
            return Response::redirect('/products');
        } catch (ValidationException $exception) {
            $product = array_merge($product, $input);

            return $this->view->render('products/form', [
                'page' => 'products',
                'mode' => 'edit',
                'product' => $product,
                'categories' => $this->categories->all(),
                'errors' => $exception->errors(),
            ], 'Ürünü Düzenle', 422);
        }
    }

    public function stock(Request $request, array $params): Response
    {
        $id = (int) ($params[0] ?? 0);
        $product = $this->products->findById($id);
        if ($product === null) {
            throw new HttpException(404, 'Ürün bulunamadı.');
        }

        return $this->view->render('products/stock', [
            'page' => 'products',
            'product' => $product,
            'movements' => $this->movements->forProduct($id),
            'errors' => [],
        ], 'Stok Hareketi');
    }

    public function storeMovement(Request $request, array $params): Response
    {
        $id = (int) ($params[0] ?? 0);
        $product = $this->products->findById($id);
        if ($product === null) {
            throw new HttpException(404, 'Ürün bulunamadı.');
        }

        $input = [
            'type' => (string) $request->post('type', ''),
            'quantity' => (string) $request->post('quantity', ''),
            'note' => (string) $request->post('note', ''),
        ];

        try {
            $this->stockService->record($id, $input);
            set_flash('success', 'Stok hareketi kaydedildi.');
            return Response::redirect('/products/' . $id . '/stock');
        } catch (ValidationException $exception) {
            return $this->view->render('products/stock', [
                'page' => 'products',
                'product' => $product,
                'movements' => $this->movements->forProduct($id),
                'errors' => $exception->errors(),
            ], 'Stok Hareketi', 422);
        } catch (DomainException $exception) {
            set_flash('error', $exception->getMessage());
            return Response::redirect('/products/' . $id . '/stock');
        }
    }

    /**
     * @return array<string, mixed>
     */
    private function productInput(Request $request): array
    {
        return [
            'name' => (string) $request->post('name', ''),
            'sku' => (string) $request->post('sku', ''),
            'category_id' => (string) $request->post('category_id', ''),
            'unit' => (string) $request->post('unit', 'adet'),
            'critical_threshold' => (string) $request->post('critical_threshold', '0'),
            'quantity' => (string) $request->post('quantity', '0'),
            'description' => (string) $request->post('description', ''),
        ];
    }
}
