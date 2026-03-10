<?php

namespace App\Controller;

use App\Service\QueueStatsService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

final class QueueController
{
    public function __construct(private readonly QueueStatsService $queueStatsService)
    {
    }

    #[Route('/api/queue/stats', name: 'queue_stats', methods: ['GET'])]
    public function stats(): JsonResponse
    {
        return new JsonResponse($this->queueStatsService->getStats());
    }
}
