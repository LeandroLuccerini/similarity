<?php

declare(strict_types=1);

namespace Szopen\Similarity;

use InvalidArgumentException;
use Szopen\Similarity\Configuration\DateFuzzySimilarityConfiguration;
use Szopen\Similarity\Normalizer\DateNormalizer;
use Szopen\Similarity\Normalizer\StringNormalizer;
use Szopen\Similarity\Normalizer\Transliterator\TransliteratorFactory;

readonly class SimilarityFactory
{
    public const STRING_EXACT = 'string-exact';
    public const STRING_FUZZY = 'string-fuzzy';
    public const DATE_FUZZY = 'date-fuzzy';

    public function __construct(private DateFuzzySimilarityConfiguration $dateFuzzySimilarityConfiguration)
    {
    }

    public function create(string $type): Similarity
    {
        return match ($type) {
            self::STRING_EXACT => new StringExactSimilarity(),
            self::STRING_FUZZY => new StringFuzzySimilarity(
                new StringNormalizer(
                    new TransliteratorFactory()
                )
            ),
            self::DATE_FUZZY => new DateFuzzySimilarity(
                $this->dateFuzzySimilarityConfiguration,
                new DateNormalizer()
            ),
            default => throw new InvalidArgumentException("Similarity type '{$type}' is not supported"),
        };
    }
}