<?php

declare(strict_types=1);

namespace Szopen\Similarity;

use DateTimeImmutable;
use Exception;
use Szopen\Similarity\Configuration\DateFuzzySimilarityConfiguration;
use Szopen\Similarity\Normalizer\DateNormalizer;

final readonly class DateFuzzySimilarity implements Similarity
{
    private const YEAR = 'Y';
    private const MONTH = 'm';
    private const DAY = 'd';

    public function __construct(
        private DateFuzzySimilarityConfiguration $configuration,
        private DateNormalizer $dateNormalizer
    ) {
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function similarity(string $a, string $b): float
    {
        $dateStringA = $this->dateNormalizer->normalize($a);
        $dateStringB = $this->dateNormalizer->normalize($b);
        if (!$dateStringA || !$dateStringB) {
            return 0.0;
        }

        $dateA = new DateTimeImmutable($dateStringA);
        $dateB = new DateTimeImmutable($dateStringB);

        $score = 0;
        foreach ($this->configuration->datePartsWeights->weights() as $part => $weight) {
            $score += $this->configuration->diffPenalty->calculate(
                $weight,
                intval($dateA->format($part)),
                intval($dateB->format($part))
            );
        }

        if ($this->areDayAndMonthSwitched($dateA, $dateB)) {
            $score = max($score, $this->configuration->maxScoreForDayMonthInversion);
        }

        return min(1.0, round($score, 3));
    }


    private function areDayAndMonthSwitched(DateTimeImmutable $dateA, DateTimeImmutable $dateB): bool
    {
        return $dateA->format(self::DAY) !== $dateB->format(self::DAY)
            && $dateA->format(self::YEAR) === $dateB->format(self::YEAR)
            && $dateA->format(self::DAY) === $dateB->format(self::MONTH)
            && $dateA->format(self::MONTH) === $dateB->format(self::DAY);
    }
}
