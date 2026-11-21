<?php

declare(strict_types=1);

namespace App\Repository;

use PDO;

final class ProductRepository
{
    public function __construct(private PDO $pdo)
    {
    }

    /**
     * @param array{search?: string, category_id?: int|null, status?: string} $filters
     * @return list<array<string, mixed>>
     */
    public function all(array $filters = []): array
    {
        $conditions = [];
        $params = [];

        $search = trim((string) ($filters['search'] ?? ''));
        if ($search !== '') {
            $conditions[] = '(p.name LIKE :search_name OR p.sku LIKE :search_sku)';
            $params['search_name'] = '%' . $search . '%';
            $params['search_sku'] = '%' . $search . '%';
        }

        $categoryId = filter_var($filters['category_id'] ?? null, FILTER_VALIDATE_INT);
        if ($categoryId !== false && $categoryId !== null && (int) $categoryId > 0) {
            $conditions[] = 'p.category_id = :category_id';
            $params['category_id'] = (int) $categoryId;
        }

        $status = (string) ($filters['status'] ?? 'all');
        if ($status === 'critical') {
            $conditions[] = 'p.quantity > 0 AND p.quantity <= p.critical_threshold';
        } elseif ($status === 'out') {
            $conditions[] = 'p.quantity = 0';
        } elseif ($status === 'available') {
            $conditions[] = 'p.quantity > 0';
        }

        $where = $conditions === [] ? '' : 'WHERE ' . implode(' AND ', $conditions);
        $sql = <<<SQL
            SELECT
                p.id,
                p.category_id,
                p.sku,
                p.name,
                p.description,
                p.quantity,
                p.critical_threshold,
                p.unit,
                p.created_at,
                p.updated_at,
                c.name AS category_name,
                CASE
                    WHEN p.quantity = 0 THEN 'out'
                    WHEN p.quantity <= p.critical_threshold THEN 'critical'
                    ELSE 'available'
                END AS stock_status
            FROM products p
            INNER JOIN categories c ON c.id = p.category_id
            {$where}
            ORDER BY p.name ASC
            SQL;

        $statement = $this->pdo->prepare($sql);
        $statement->execute($params);

        return $statement->fetchAll();
    }

    /**
     * @return array<string, mixed>|null
     */
    public function findById(int $id): ?array
    {
        $statement = $this->pdo->prepare(
            <<<'SQL'
                SELECT
                    p.id,
                    p.category_id,
                    p.sku,
                    p.name,
                    p.description,
                    p.quantity,
                    p.critical_threshold,
                    p.unit,
                    p.created_at,
                    p.updated_at,
                    c.name AS category_name
                FROM products p
                INNER JOIN categories c ON c.id = p.category_id
                WHERE p.id = :id
                LIMIT 1
                SQL
        );
        $statement->execute(['id' => $id]);
        $product = $statement->fetch();

        return $product === false ? null : $product;
    }

    /**
     * @return array<string, mixed>|null
     */
    public function findForUpdate(int $id): ?array
    {
        $statement = $this->pdo->prepare(
            <<<'SQL'
                SELECT id, sku, name, quantity, critical_threshold, unit
                FROM products
                WHERE id = :id
                LIMIT 1
                FOR UPDATE
                SQL
        );
        $statement->execute(['id' => $id]);
        $product = $statement->fetch();

        return $product === false ? null : $product;
    }

    /**
     * @param array<string, mixed> $data
     */
    public function create(array $data): int
    {
        $statement = $this->pdo->prepare(
            <<<'SQL'
                INSERT INTO products (
                    category_id,
                    sku,
                    name,
                    description,
                    quantity,
                    critical_threshold,
                    unit
                ) VALUES (
                    :category_id,
                    :sku,
                    :name,
                    :description,
                    :quantity,
                    :critical_threshold,
                    :unit
                )
                SQL
        );
        $statement->execute([
            'category_id' => (int) $data['category_id'],
            'sku' => (string) $data['sku'],
            'name' => (string) $data['name'],
            'description' => $data['description'] !== '' ? $data['description'] : null,
            'quantity' => (int) $data['quantity'],
            'critical_threshold' => (int) $data['critical_threshold'],
            'unit' => (string) $data['unit'],
        ]);

        return (int) $this->pdo->lastInsertId();
    }

    /**
     * @param array<string, mixed> $data
     */
    public function update(int $id, array $data): bool
    {
        $statement = $this->pdo->prepare(
            <<<'SQL'
                UPDATE products
                SET
                    category_id = :category_id,
                    sku = :sku,
                    name = :name,
                    description = :description,
                    critical_threshold = :critical_threshold,
                    unit = :unit,
                    updated_at = CURRENT_TIMESTAMP
                WHERE id = :id
                SQL
        );
        $statement->execute([
            'id' => $id,
            'category_id' => (int) $data['category_id'],
            'sku' => (string) $data['sku'],
            'name' => (string) $data['name'],
            'description' => $data['description'] !== '' ? $data['description'] : null,
            'critical_threshold' => (int) $data['critical_threshold'],
            'unit' => (string) $data['unit'],
        ]);

        return $statement->rowCount() > 0;
    }

    public function updateQuantity(int $id, int $quantity): bool
    {
        $statement = $this->pdo->prepare(
            'UPDATE products SET quantity = :quantity, updated_at = CURRENT_TIMESTAMP WHERE id = :id'
        );
        $statement->execute([
            'id' => $id,
            'quantity' => $quantity,
        ]);

        return $statement->rowCount() > 0;
    }
}
