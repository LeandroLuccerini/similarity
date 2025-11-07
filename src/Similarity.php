<?php

declare(strict_types=1);

namespace Szopen\Similarity;

use InvalidArgumentException;

interface Similarity
{
    /**
     * @throws InvalidArgumentException
     */
    public function similarity(string $a, string $b): float;
}
