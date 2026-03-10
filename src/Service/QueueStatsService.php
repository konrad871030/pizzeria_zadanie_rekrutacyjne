<?php

namespace App\Service;

use App\Repository\OrderQueueReader;

final class QueueStatsService
{
    private const PROCESSING_MINUTES_PER_ORDER = 10;

    public function __construct(private readonly OrderQueueReader $orderRepository)
    {
    }

    /**
     * @return array{pendingCount:int,etaMinutes:int}
     */
    public function getStats(): array
    {
        $pendingCount = $this->orderRepository->countPending();

        return [
            'pendingCount' => $pendingCount,
            'etaMinutes' => $pendingCount * self::PROCESSING_MINUTES_PER_ORDER,
        ];
    }
}
