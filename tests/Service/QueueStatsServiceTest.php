<?php

namespace App\Tests\Service;

use App\Repository\OrderQueueReader;
use App\Service\QueueStatsService;
use PHPUnit\Framework\TestCase;

final class QueueStatsServiceTest extends TestCase
{
    public function testItCalculatesQueueStats(): void
    {
        $reader = new class() implements OrderQueueReader {
            public function countPending(): int
            {
                return 3;
            }
        };

        $service = new QueueStatsService($reader);
        $stats = $service->getStats();

        self::assertSame(3, $stats['pendingCount']);
        self::assertSame(30, $stats['etaMinutes']);
    }
}
