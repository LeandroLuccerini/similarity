<?php

declare(strict_types=1);

namespace Szopen\Similarity\Normalizer;

interface Normalizer
{
    public function normalize(string $string): ?string;
}