<?php

namespace App\Infrastructure\Symfony\Repository;

use Countable;
use Iterator;
use IteratorAggregate;

interface Repository extends IteratorAggregate, Countable
{
    public function getIterator(): Iterator;

    public function slice(int $start, int $size = 20): self;

    public function count(): int;
}