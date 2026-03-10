<?php

namespace App\Service;

use App\Repository\MenuItemRepository;
use App\Repository\OrderRepository;
use InvalidArgumentException;

final class OrderService
{
    public function __construct(
        private readonly MenuItemRepository $menuItemRepository,
        private readonly OrderRepository $orderRepository,
    ) {
    }

    public function createOrder(int $menuItemId, int $quantity, string $email, string $deliveryAddress): void
    {
        if ($quantity < 1) {
            throw new InvalidArgumentException('Quantity must be at least 1.');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('Email is invalid.');
        }

        if (trim($deliveryAddress) === '') {
            throw new InvalidArgumentException('Delivery address is required.');
        }

        if ($this->menuItemRepository->findById($menuItemId) === null) {
            throw new InvalidArgumentException('Selected pizza does not exist.');
        }

        $this->orderRepository->create($menuItemId, $quantity, trim($email), trim($deliveryAddress));
    }
}
