<?php

declare(strict_types=1);

namespace Szopen\Similarity\Normalizer;

use Normalizer;
use Transliterator;

readonly class StringNormalizer
{
    public function __construct(private ClassChecker $classChecker)
    {
    }

    public function normalize(string $string): ?string
    {
        $string = trim($string);
        if ($string === '') {
            return null;
        }

        $string = $this->removeAllSpaces(
            $this->removeNonAlphanumericChars(
                $this->transliterate(
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

    private function transliterate(string $string): string
    {
        // Transliterate to ASCII to avoid multibyte issues with levenshtein
        // Prefer Transliterator if available
        if ($this->classChecker->exists('Transliterator')) {
            $trans = Transliterator::create('Any-Latin; Latin-ASCII;');
            if ($trans) {
                return $trans->transliterate($string) ?: $string;
            }
        } else {
            return iconv(
                'UTF-8',
                'ASCII//TRANSLIT//IGNORE',
                $string
            ) ?: $string;
        }

        return $string;
    }

    private function tryNfcNormalizationIfAvailable(string $s): string
    {
        if ($this->classChecker->exists('Normalizer')) {
            return $s;
        }
        // Unicode normalization (NFC)
        // @phpstan-ignore return.type
        return Normalizer::normalize($s, Normalizer::FORM_C) ?: $s;
    }

    private function multibyteLowercase(string $s): string
    {
        return mb_strtolower($s, 'UTF-8');
    }
}
