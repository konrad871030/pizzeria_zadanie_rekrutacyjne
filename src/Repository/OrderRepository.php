<?php

namespace App\Repository;

use App\Entity\Order;
use App\Service\DbConnectionFactory;

final class OrderRepository implements OrderQueueReader
{
    public function __construct(private readonly DbConnectionFactory $connectionFactory)
    {
    }

    public function create(int $menuItemId, int $quantity, string $email, string $deliveryAddress): void
    {
        $stmt = $this->connectionFactory->getConnection()->prepare(
            'INSERT INTO orders (menu_item_id, quantity, email, delivery_address, status, created_at) VALUES (:menu_item_id, :quantity, :email, :delivery_address, :status, NOW())'
        );

        $stmt->execute([
            'menu_item_id' => $menuItemId,
            'quantity' => $quantity,
            'email' => $email,
            'delivery_address' => $deliveryAddress,
            'status' => Order::STATUS_PENDING,
        ]);
    }

    public function countPending(): int
    {
        $stmt = $this->connectionFactory->getConnection()->query(
            sprintf("SELECT COUNT(*) FROM orders WHERE status = '%s'", Order::STATUS_PENDING)
        );

        return (int) $stmt->fetchColumn();
    }

    public function findOldestPendingId(): ?int
    {
        $stmt = $this->connectionFactory->getConnection()->query(
            sprintf(
                "SELECT id FROM orders WHERE status = '%s' ORDER BY created_at ASC, id ASC LIMIT 1",
                Order::STATUS_PENDING
            )
        );
        $id = $stmt->fetchColumn();

        return $id === false ? null : (int) $id;
    }

    public function markDelivered(int $id): void
    {
        $stmt = $this->connectionFactory->getConnection()->prepare(
            sprintf(
                "UPDATE orders SET status = '%s', delivered_at = NOW() WHERE id = :id",
                Order::STATUS_DELIVERED
            )
        );
        $stmt->execute(['id' => $id]);
    }
}
