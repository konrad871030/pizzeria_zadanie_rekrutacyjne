<?php

namespace App\Entity;

final class MenuItem
{
    public function __construct(
        public int $id,
        public string $name,
        public int $priceCents,
        public string $ingredients,
        public string $imageName,
    ) {
    }
}
