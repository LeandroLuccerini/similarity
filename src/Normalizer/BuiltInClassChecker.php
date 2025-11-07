<?php

declare(strict_types=1);

namespace Szopen\Similarity\Normalizer;

readonly final class BuiltInClassChecker implements ClassChecker
{

    public function exists(string $class): bool
    {
        return class_exists($class);
    }
}