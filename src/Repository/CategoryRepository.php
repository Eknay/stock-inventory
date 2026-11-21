<?php

declare(strict_types=1);

namespace App\Repository;

use PDO;

final class CategoryRepository
{
    public function __construct(private PDO $pdo)
    {
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function all(): array
    {
        $sql = <<<'SQL'
            SELECT
                c.id,
                c.name,
                c.description,
                COUNT(p.id) AS product_count
            FROM categories c
            LEFT JOIN products p ON p.category_id = c.id
            GROUP BY c.id, c.name, c.description
            ORDER BY c.name ASC
            SQL;

        $statement = $this->pdo->prepare($sql);
        $statement->execute();

        return $statement->fetchAll();
    }

    /**
     * @return array<string, mixed>|null
     */
    public function findById(int $id): ?array
    {
        $statement = $this->pdo->prepare(
            'SELECT id, name, description FROM categories WHERE id = :id LIMIT 1'
        );
        $statement->execute(['id' => $id]);
        $category = $statement->fetch();

        return $category === false ? null : $category;
    }

    public function create(string $name, ?string $description): int
    {
        $statement = $this->pdo->prepare(
            'INSERT INTO categories (name, description) VALUES (:name, :description)'
        );
        $statement->execute([
            'name' => $name,
            'description' => $description !== '' ? $description : null,
        ]);

        return (int) $this->pdo->lastInsertId();
    }
}
