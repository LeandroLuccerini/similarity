<?php

declare(strict_types=1);

namespace Tests\Szopen\Similarity;

use Szopen\Similarity\Normalizer\ClassChecker;

final readonly class DesiredValuedClassCheckerStub implements ClassChecker
{
    public function __construct(private bool $result)
    {
    }

    public function exists(string $class): bool
    {
        return $this->result;
    }
}