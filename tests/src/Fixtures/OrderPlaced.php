<?php

namespace TomatoPHP\FilamentWorkflows\Tests\Fixtures;

class OrderPlaced
{
    public function __construct(
        public int $orderId,
        public float $total,
    ) {}
}
