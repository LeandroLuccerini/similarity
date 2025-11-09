<?php

declare(strict_types=1);

namespace Szopen\Similarity\Normalizer\Transliterator;

use Transliterator as BaseIntlTransliterator;

readonly class TransliteratorFactory
{
    public function create(): Transliterator
    {
        if (class_exists(BaseIntlTransliterator::class)) {
            return new IntlTransliterator();
        }

        return new IconvTransliterator();
    }
}
