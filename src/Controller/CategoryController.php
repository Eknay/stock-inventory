<?php

declare(strict_types=1);

namespace App\Controller;

use App\Exception\ValidationException;
use App\Http\Request;
use App\Http\Response;
use App\Repository\CategoryRepository;
use App\Service\CategoryService;
use App\View;

final class CategoryController
{
    public function __construct(
        private CategoryRepository $categories,
        private CategoryService $categoryService,
        private View $view
    ) {
    }

    public function index(Request $request, array $params = []): Response
    {
        return $this->view->render('categories/index', [
            'page' => 'categories',
            'categories' => $this->categories->all(),
            'errors' => [],
            'old' => [
                'name' => '',
                'description' => '',
            ],
        ], 'Kategoriler');
    }

    public function store(Request $request, array $params = []): Response
    {
        $input = [
            'name' => (string) $request->post('name', ''),
            'description' => (string) $request->post('description', ''),
        ];

        try {
            $this->categoryService->create($input);
            set_flash('success', 'Kategori başarıyla eklendi.');
            return Response::redirect('/categories');
        } catch (ValidationException $exception) {
            return $this->view->render('categories/index', [
                'page' => 'categories',
                'categories' => $this->categories->all(),
                'errors' => $exception->errors(),
                'old' => $input,
            ], 'Kategoriler', 422);
        }
    }
}
