<?php

declare(strict_types=1);

namespace Szopen\Similarity\Normalizer;

interface ClassChecker
{
    public function exists(string $class): bool;
}
