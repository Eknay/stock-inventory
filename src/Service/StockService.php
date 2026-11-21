<?php

declare(strict_types=1);

namespace App\Service;

use App\Database;
use App\Exception\ValidationException;
use App\Repository\ProductRepository;
use App\Repository\StockMovementRepository;
use DomainException;

final class StockService
{
    public function __construct(
        private Database $database,
        private ProductRepository $products,
        private StockMovementRepository $movements
    ) {
    }

    /**
     * @param array<string, mixed> $input
     */
    public function record(int $productId, array $input): int
    {
        $type = $this->stringValue($input['type'] ?? '');
        $quantity = filter_var(
            $input['quantity'] ?? null,
            FILTER_VALIDATE_INT,
            ['options' => ['min_range' => 1, 'max_range' => 1000000000]]
        );
        $note = $this->stringValue($input['note'] ?? '');

        $errors = [];
        if (!in_array($type, ['in', 'out'], true)) {
            $errors['type'] = 'Hareket türü seçin.';
        }
        if ($quantity === false) {
            $errors['quantity'] = 'Miktar 1 veya daha büyük bir tam sayı olmalıdır.';
        }
        if (text_length($note) > 500) {
            $errors['note'] = 'Not en fazla 500 karakter olabilir.';
        }

        if ($errors !== []) {
            throw new ValidationException($errors);
        }

        /** @var int $quantity */
        return $this->database->transaction(function () use ($productId, $type, $quantity, $note): int {
            $product = $this->products->findForUpdate($productId);
            if ($product === null) {
                throw new DomainException('Ürün bulunamadı.');
            }

            $currentQuantity = (int) $product['quantity'];
            $newQuantity = $type === 'in'
                ? $currentQuantity + $quantity
                : $currentQuantity - $quantity;

            if ($newQuantity < 0) {
                throw new DomainException(
                    sprintf('Yetersiz stok. Mevcut miktar: %d; çıkış miktarı: %d.', $currentQuantity, $quantity)
                );
            }

            $this->products->updateQuantity($productId, $newQuantity);

            return $this->movements->create($productId, $type, $quantity, $note);
        });
    }

    private function stringValue(mixed $value): string
    {
        return is_scalar($value) ? trim((string) $value) : '';
    }
}
