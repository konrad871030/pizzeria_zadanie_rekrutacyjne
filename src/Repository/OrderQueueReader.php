<?php

namespace App\Repository;

interface OrderQueueReader
{
    public function countPending(): int;
}
