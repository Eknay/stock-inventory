<?php

declare(strict_types=1);

namespace App\Repository;

use PDO;

final class DashboardRepository
{
    public function __construct(private PDO $pdo)
    {
    }

    /**
     * @return array<string, int>
     */
    public function summary(): array
    {
        $statement = $this->pdo->query(
            <<<'SQL'
                SELECT
                    COUNT(*) AS total_products,
                    COALESCE(SUM(quantity), 0) AS total_units,
                    COALESCE(SUM(CASE WHEN quantity > 0 AND quantity <= critical_threshold THEN 1 ELSE 0 END), 0) AS critical_stock,
                    COALESCE(SUM(CASE WHEN quantity = 0 THEN 1 ELSE 0 END), 0) AS out_of_stock
                FROM products
                SQL
        );
        $summary = $statement->fetch() ?: [];

        $categoryStatement = $this->pdo->query('SELECT COUNT(*) AS total_categories FROM categories');
        $categoryRow = $categoryStatement->fetch() ?: [];

        return [
            'total_products' => (int) ($summary['total_products'] ?? 0),
            'total_units' => (int) ($summary['total_units'] ?? 0),
            'critical_stock' => (int) ($summary['critical_stock'] ?? 0),
            'out_of_stock' => (int) ($summary['out_of_stock'] ?? 0),
            'total_categories' => (int) ($categoryRow['total_categories'] ?? 0),
        ];
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function categoryBreakdown(): array
    {
        $statement = $this->pdo->query(
            <<<'SQL'
                SELECT
                    c.name AS category_name,
                    COUNT(p.id) AS product_count,
                    COALESCE(SUM(p.quantity), 0) AS total_units,
                    COALESCE(SUM(CASE WHEN p.quantity > 0 AND p.quantity <= p.critical_threshold THEN 1 ELSE 0 END), 0) AS critical_count
                FROM categories c
                LEFT JOIN products p ON p.category_id = c.id
                GROUP BY c.id, c.name
                ORDER BY total_units DESC, c.name ASC
                SQL
        );

        return $statement->fetchAll();
    }
}
