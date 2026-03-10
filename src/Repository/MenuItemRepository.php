<?php

namespace App\Repository;

use App\Entity\MenuItem;
use App\Service\DbConnectionFactory;

final class MenuItemRepository
{
    public function __construct(private readonly DbConnectionFactory $connectionFactory)
    {
    }

    /**
     * @return array<MenuItem>
     */
    public function findAll(): array
    {
        $stmt = $this->connectionFactory->getConnection()->query('SELECT id, name, price_cents FROM menu_items ORDER BY id ASC');
        $rows = $stmt->fetchAll();

        return array_map(
            static fn (array $row): MenuItem => new MenuItem((int) $row['id'], (string) $row['name'], (int) $row['price_cents']),
            $rows
        );
    }

    public function findById(int $id): ?MenuItem
    {
        $stmt = $this->connectionFactory->getConnection()->prepare('SELECT id, name, price_cents FROM menu_items WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();

        if ($row === false) {
            return null;
        }

        return new MenuItem((int) $row['id'], (string) $row['name'], (int) $row['price_cents']);
    }

    /**
     * @param array<array{name: string, price_cents: int}> $items
     */
    public function replaceAll(array $items): void
    {
        $pdo = $this->connectionFactory->getConnection();
        $pdo->beginTransaction();

        try {
            // Reset the queue state before replacing menu rows referenced by orders.
            $pdo->exec('DELETE FROM orders');
            $pdo->exec('DELETE FROM menu_items');

            $stmt = $pdo->prepare('INSERT INTO menu_items (name, price_cents) VALUES (:name, :price_cents)');
            foreach ($items as $item) {
                $stmt->execute([
                    'name' => $item['name'],
                    'price_cents' => $item['price_cents'],
                ]);
            }

            $pdo->commit();
        } catch (\Throwable $e) {
            $pdo->rollBack();
            throw $e;
        }
    }
}
