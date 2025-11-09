<?php

declare(strict_types=1);

namespace Szopen\Similarity\Normalizer\Transliterator;

final readonly class IconvTransliterator implements Transliterator
{
    public function transliterate(string $string): string
    {
        return iconv(
            'UTF-8',
            'ASCII//TRANSLIT//IGNORE',
            $string
        ) ?: $string;
    }
}
