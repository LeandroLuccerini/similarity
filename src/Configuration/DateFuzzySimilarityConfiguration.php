<?php

declare(strict_types=1);

namespace Szopen\Similarity\Configuration;

readonly class DateFuzzySimilarityConfiguration
{
    public function __construct(
        public DatePartsWeights $datePartsWeights,
        public DateDiffPenalty $diffPenalty,
        public float $maxScoreForDayMonthInversion = 0.8
    ) {
    }
}