<?php

declare(strict_types=1);

namespace App\Validation;

final class CategoryValidator
{
    /**
     * @param array<string, mixed> $input
     * @return array<string, string>
     */
    public function validate(array $input): array
    {
        $errors = [];
        $name = $this->stringValue($input['name'] ?? '');
        $description = $this->stringValue($input['description'] ?? '');

        if ($name === '') {
            $errors['name'] = 'Kategori adı zorunludur.';
        } elseif (text_length($name) > 100) {
            $errors['name'] = 'Kategori adı en fazla 100 karakter olabilir.';
        }

        if (text_length($description) > 500) {
            $errors['description'] = 'Açıklama en fazla 500 karakter olabilir.';
        }

        return $errors;
    }

    private function stringValue(mixed $value): string
    {
        return is_scalar($value) ? trim((string) $value) : '';
    }
}
