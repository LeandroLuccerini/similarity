<?php

declare(strict_types=1);

namespace Szopen\Similarity\Normalizer;

use Normalizer as IntlNormalizer;
use Szopen\Similarity\Normalizer\Transliterator\TransliteratorFactory;

final readonly class StringNormalizer implements Normalizer
{
    public function __construct(private TransliteratorFactory $transliteratorFactory)
    {
    }

    public function normalize(string $string): ?string
    {
        $string = trim($string);
        if ($string === '') {
            return null;
        }

        $transliterator = $this->transliteratorFactory->create();
        $string = $this->removeAllSpaces(
            $this->removeNonAlphanumericChars(
                $transliterator->transliterate(
                    $this->tryNfcNormalizationIfAvailable(
                        $this->multibyteLowercase($string)
                    )
                )
            )
        );

        return $string === '' ? null : $string;
    }

    private function removeAllSpaces(string $string): string
    {
        $noSpaces = preg_replace(
            '/\s+/',
            '',
            $string
        );
        return trim($noSpaces !== null ? $noSpaces : $string);
    }

    private function removeNonAlphanumericChars(string $string): string
    {
        $alphanumeric = preg_replace(
            '/[^A-Za-z0-9 ]+/',
            '',
            $string
        );

        return $alphanumeric !== null ? $alphanumeric : $string;
    }

    private function tryNfcNormalizationIfAvailable(string $s): string
    {
        if ($this->classExists('Normalizer')) {
            return $s;
        }
        // Unicode normalization (NFC)
        // @phpstan-ignore return.type
        return IntlNormalizer::normalize($s, IntlNormalizer::FORM_C) ?: $s;
    }

    private function classExists(string $class): bool
    {
        return class_exists($class);
    }

    private function multibyteLowercase(string $s): string
    {
        return mb_strtolower($s, 'UTF-8');
    }
}
