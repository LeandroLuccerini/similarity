<?php

declare(strict_types=1);

namespace Szopen\Similarity;

use Normalizer;
use Transliterator;

readonly class StringNormalizer
{
    public function normalize(string $string): ?string
    {
        $string = trim($string);
        if ($string === '') {
            return null;
        }

        $string = $this->removeExtraSpaces(
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

    private function removeExtraSpaces(string $string): string
    {
        return trim(preg_replace('/\s+/', '', $string));
    }

    private function removeNonAlphanumericChars(string $string): string
    {
        return preg_replace('/[^A-Za-z0-9 ]+/', '', $string);
    }

    private function transliterate(string $string): string
    {
        // Transliterate to ASCII to avoid multibyte issues with levenshtein
        // Prefer Transliterator if available
        if (class_exists('Transliterator')) {
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
        if (!class_exists('Normalizer')) {
            return $s;
        }
        // Unicode normalization (NFC)
        return Normalizer::normalize($s, Normalizer::FORM_C) ?: $s;
    }

    private function multibyteLowercase(string $s): string
    {
        return mb_strtolower($s, 'UTF-8');
    }
}