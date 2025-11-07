<?php

declare(strict_types=1);

namespace Szopen\Similarity;

use InvalidArgumentException;
use Szopen\Similarity\Normalizer\StringNormalizer;

final readonly class FuzzySimilarity implements Similarity
{
    public function __construct(private StringNormalizer $stringNormalizer)
    {
    }

    public function similarity(string $a, string $b): float
    {
        $normalizedA = $this->stringNormalizer->normalize($a);
        $normalizedB = $this->stringNormalizer->normalize($b);

        $this->assertValidNormalization($normalizedA, $a, $normalizedB, $b);

        if ($normalizedA === $normalizedB) {
            return 1.0;
        }

        assert(is_string($normalizedA)); // Fools phpstan, if null assertValidNormalization throws exception
        assert(is_string($normalizedB)); // Fools phpstan, if null assertValidNormalization throws exception

        // For short string similar_text is more efficient and more stable than levenshtein
        if ($this->stringsAreVeryShort($normalizedA, $normalizedB)) {
            similar_text($normalizedA, $normalizedB, $percent);

            return round($percent / 100.0, 3);
        }

        return $this->levenshteinSimilarity($normalizedA, $normalizedB);
    }

    private function assertValidNormalization(
        ?string $normalizedA,
        string $a,
        ?string $normalizedB,
        string $b
    ): void {
        if ($normalizedA === null) {
            throw new InvalidArgumentException(
                sprintf("String '%s' is not a normalizable string.", $a)
            );
        }

        if ($normalizedB === null) {
            throw new InvalidArgumentException(
                sprintf("String '%s' is not a normalizable string.", $b)
            );
        }
    }

    private function stringsAreVeryShort(string $normalizedA, string $normalizedB): bool
    {
        return max(
            mb_strlen($normalizedA, 'UTF-8'),
            mb_strlen($normalizedB, 'UTF-8')
        ) <= 2;
    }

    private function levenshteinSimilarity(string $normalizedA, string $normalizedB): float
    {
        $distance = levenshtein($normalizedA, $normalizedB);
        $maxLen = max(strlen($normalizedA), strlen($normalizedB));
        $sim = round(1 - ($distance / $maxLen), 3);
        return max(0.0, min(1.0, $sim));
    }
}
