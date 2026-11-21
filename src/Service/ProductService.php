<?php

declare(strict_types=1);

namespace App\Service;

use App\Database;
use App\Exception\ValidationException;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use App\Repository\StockMovementRepository;
use App\Validation\ProductValidator;
use PDOException;

final class ProductService
{
    public function __construct(
        private Database $database,
        private ProductRepository $products,
        private CategoryRepository $categories,
        private StockMovementRepository $movements,
        private ProductValidator $validator
    ) {
    }

    /**
     * @param array<string, mixed> $input
     */
    public function create(array $input): int
    {
        $errors = $this->validator->validate($input, true);
        if ($errors !== []) {
            throw new ValidationException($errors);
        }

        $data = $this->normalize($input);
        if ($this->categories->findById($data['category_id']) === null) {
            throw new ValidationException([
                'category_id' => 'Seçilen kategori bulunamadı.',
            ]);
        }

        try {
            return $this->database->transaction(function () use ($data): int {
                $productId = $this->products->create($data);

                if ($data['quantity'] > 0) {
                    $this->movements->create(
                        $productId,
                        'in',
                        $data['quantity'],
                        'Ürün oluşturulurken açılış stoğu'
                    );
                }

                return $productId;
            });
        } catch (PDOException $exception) {
            if ((int) ($exception->errorInfo[1] ?? 0) === 1062) {
                throw new ValidationException([
                    'sku' => 'Bu SKU zaten kullanılıyor.',
                ]);
            }

            throw $exception;
        }
    }

    /**
     * @param array<string, mixed> $input
     */
    public function update(int $id, array $input): void
    {
        $errors = $this->validator->validate($input, false);
        if ($errors !== []) {
            throw new ValidationException($errors);
        }

        $data = $this->normalize($input);
        if ($this->categories->findById($data['category_id']) === null) {
            throw new ValidationException([
                'category_id' => 'Seçilen kategori bulunamadı.',
            ]);
        }

        try {
            $this->database->transaction(function () use ($id, $data): void {
                $this->products->update($id, $data);
            });
        } catch (PDOException $exception) {
            if ((int) ($exception->errorInfo[1] ?? 0) === 1062) {
                throw new ValidationException([
                    'sku' => 'Bu SKU zaten kullanılıyor.',
                ]);
            }

            throw $exception;
        }
    }

    /**
     * @param array<string, mixed> $input
     * @return array{category_id: int, sku: string, name: string, description: string, quantity: int, critical_threshold: int, unit: string}
     */
    private function normalize(array $input): array
    {
        return [
            'category_id' => (int) $input['category_id'],
            'sku' => strtoupper(trim((string) $input['sku'])),
            'name' => trim((string) $input['name']),
            'description' => is_scalar($input['description'] ?? '')
                ? trim((string) $input['description'])
                : '',
            'quantity' => (int) ($input['quantity'] ?? 0),
            'critical_threshold' => (int) $input['critical_threshold'],
            'unit' => trim((string) $input['unit']),
        ];
    }
}
