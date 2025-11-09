<?php

declare(strict_types=1);

namespace Szopen\Similarity\Normalizer;

use Transliterator as BaseIntlTransliterator;

class IntlTransliterator implements Transliterator
{
    public function transliterate(string $string): string
    {
        $trans = BaseIntlTransliterator::create('Any-Latin; Latin-ASCII;');
        if ($trans) {
            return $trans->transliterate($string) ?: $string;
        }

        return $string;
    }
}