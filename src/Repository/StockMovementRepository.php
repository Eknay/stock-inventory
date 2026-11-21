<?php

declare(strict_types=1);

namespace App\Repository;

use PDO;

final class StockMovementRepository
{
    public function __construct(private PDO $pdo)
    {
    }

    public function create(
        int $productId,
        string $type,
        int $quantity,
        ?string $note = null
    ): int {
        $statement = $this->pdo->prepare(
            <<<'SQL'
                INSERT INTO stock_movements (product_id, type, quantity, note)
                VALUES (:product_id, :type, :quantity, :note)
                SQL
        );
        $statement->execute([
            'product_id' => $productId,
            'type' => $type,
            'quantity' => $quantity,
            'note' => $note !== '' ? $note : null,
        ]);

        return (int) $this->pdo->lastInsertId();
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function recent(int $limit = 10): array
    {
        $limit = max(1, min($limit, 100));
        $statement = $this->pdo->prepare(
            <<<'SQL'
                SELECT
                    sm.id,
                    sm.product_id,
                    sm.type,
                    sm.quantity,
                    sm.note,
                    sm.created_at,
                    p.sku,
                    p.name AS product_name,
                    p.unit
                FROM stock_movements sm
                INNER JOIN products p ON p.id = sm.product_id
                ORDER BY sm.created_at DESC, sm.id DESC
                LIMIT :limit
                SQL
        );
        $statement->bindValue(':limit', $limit, PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetchAll();
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function forProduct(int $productId, int $limit = 20): array
    {
        $limit = max(1, min($limit, 100));
        $statement = $this->pdo->prepare(
            <<<'SQL'
                SELECT
                    id,
                    type,
                    quantity,
                    note,
                    created_at
                FROM stock_movements
                WHERE product_id = :product_id
                ORDER BY created_at DESC, id DESC
                LIMIT :limit
                SQL
        );
        $statement->bindValue(':product_id', $productId, PDO::PARAM_INT);
        $statement->bindValue(':limit', $limit, PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetchAll();
    }
}
