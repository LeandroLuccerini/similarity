<?php

declare(strict_types=1);

namespace Szopen\Similarity\Normalizer\Transliterator;

interface Transliterator
{
    public function transliterate(string $string): string;
}