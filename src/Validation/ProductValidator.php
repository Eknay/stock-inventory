<?php

declare(strict_types=1);

namespace App\Validation;

final class ProductValidator
{
    /**
     * @param array<string, mixed> $input
     * @return array<string, string>
     */
    public function validate(array $input, bool $includeQuantity = true): array
    {
        $errors = [];

        $name = $this->stringValue($input['name'] ?? '');
        if ($name === '') {
            $errors['name'] = 'Ürün adı zorunludur.';
        } elseif ($this->textLength($name) > 150) {
            $errors['name'] = 'Ürün adı en fazla 150 karakter olabilir.';
        }

        $sku = strtoupper($this->stringValue($input['sku'] ?? ''));
        if ($sku === '') {
            $errors['sku'] = 'SKU zorunludur.';
        } elseif (!preg_match('/^[A-Z0-9][A-Z0-9._-]{2,31}$/', $sku)) {
            $errors['sku'] = 'SKU 3-32 karakter olmalı; harf, rakam, nokta, alt çizgi ve tire kullanabilirsiniz.';
        }

        $categoryId = filter_var(
            $input['category_id'] ?? null,
            FILTER_VALIDATE_INT,
            ['options' => ['min_range' => 1]]
        );
        if ($categoryId === false) {
            $errors['category_id'] = 'Geçerli bir kategori seçmelisiniz.';
        }

        $unit = $this->stringValue($input['unit'] ?? '');
        if ($unit === '') {
            $errors['unit'] = 'Birim zorunludur.';
        } elseif ($this->textLength($unit) > 20) {
            $errors['unit'] = 'Birim en fazla 20 karakter olabilir.';
        }

        $threshold = filter_var(
            $input['critical_threshold'] ?? null,
            FILTER_VALIDATE_INT,
            ['options' => ['min_range' => 0, 'max_range' => 1000000000]]
        );
        if ($threshold === false) {
            $errors['critical_threshold'] = 'Kritik stok eşiği 0 veya daha büyük bir tam sayı olmalıdır.';
        }

        $description = $this->stringValue($input['description'] ?? '');
        if ($this->textLength($description) > 1000) {
            $errors['description'] = 'Açıklama en fazla 1.000 karakter olabilir.';
        }

        if ($includeQuantity) {
            $quantity = filter_var(
                $input['quantity'] ?? null,
                FILTER_VALIDATE_INT,
                ['options' => ['min_range' => 0, 'max_range' => 1000000000]]
            );
            if ($quantity === false) {
                $errors['quantity'] = 'Stok miktarı 0 veya daha büyük bir tam sayı olmalıdır.';
            }
        }

        return $errors;
    }

    private function textLength(string $value): int
    {
        $length = preg_match_all('/./us', $value);

        return $length === false ? strlen($value) : $length;
    }

    private function stringValue(mixed $value): string
    {
        return is_scalar($value) ? trim((string) $value) : '';
    }
}
