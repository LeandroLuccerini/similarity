<?php

declare(strict_types=1);

namespace Szopen\Similarity\Normalizer;

class IconvTransliterator implements Transliterator
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