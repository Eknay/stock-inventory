<?php

declare(strict_types=1);

namespace App\Service;

use App\Exception\ValidationException;
use App\Repository\CategoryRepository;
use App\Validation\CategoryValidator;
use PDOException;

final class CategoryService
{
    public function __construct(
        private CategoryRepository $categories,
        private CategoryValidator $validator
    ) {
    }

    /**
     * @param array<string, mixed> $input
     */
    public function create(array $input): int
    {
        $errors = $this->validator->validate($input);
        if ($errors !== []) {
            throw new ValidationException($errors);
        }

        $name = is_scalar($input['name']) ? trim((string) $input['name']) : '';
        $description = is_scalar($input['description'] ?? '')
            ? trim((string) $input['description'])
            : '';

        try {
            return $this->categories->create($name, $description);
        } catch (PDOException $exception) {
            if ((int) ($exception->errorInfo[1] ?? 0) === 1062) {
                throw new ValidationException([
                    'name' => 'Bu kategori adı zaten kullanılıyor.',
                ]);
            }

            throw $exception;
        }
    }
}
