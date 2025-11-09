<?php

declare(strict_types=1);

namespace Szopen\Similarity\Configuration;

readonly class DateDiffPenalty
{
    public function __construct(
        private int $maxDatePartDiffAccepted = 5,
        private float $diffPenalty = 0.7,
    ) {
    }

    public function calculate(
        float $weight,
        int $datePartA,
        int $datePartB
    ): float {
        $diff = abs($datePartA - $datePartB);
        if ($diff === 0) {
            return $weight;
        }

        for ($i = 1; $i <= $this->maxDatePartDiffAccepted; $i++) {
            if ($diff === $i) {
                return $weight * $this->diffPenalty ^ $i;
            }
        }

        return 0.0;
    }
}