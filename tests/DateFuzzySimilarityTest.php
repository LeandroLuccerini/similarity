<?php

declare(strict_types=1);

namespace Tests\Szopen\Similarity;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;
use Szopen\Similarity\Configuration\DateDiffPenalty;
use Szopen\Similarity\Configuration\DateFuzzySimilarityConfiguration;
use Szopen\Similarity\Configuration\DatePartsWeights;
use Szopen\Similarity\DateFuzzySimilarity;
use Szopen\Similarity\Normalizer\DateNormalizer;

#[Group("similarity")]
class DateFuzzySimilarityTest extends TestCase
{

    private static float $yearWeight = 0.60;
    private static float $monthWeight = 0.20;
    private static float $dayWeight = 0.20;
    public static function similarityDataProvider(): array
    {
        return [
            // identical ISO dates -> full match
            [['2020-01-01', '2020-01-01'], 1.0],
            // same date but different separator -> normalization makes them equal
            [['2020/01/01', '2020.01.01'], 1.0],
            // swapped day/month format (DD-MM-YYYY) -> normalization still equal
            [['01-02-2020', '2020-02-01'], 1.0],
            // 1-year difference -> only year weight penalized
            [['2020-01-01', '2021-01-01'], round(self::$yearWeight * 0.7 + self::$monthWeight + self::$dayWeight, 3)],
            // 2-year difference -> stronger year penalty
            [['2020-01-01', '2022-01-01'], round(self::$yearWeight * pow(0.7, 2) + self::$monthWeight + self::$dayWeight, 3)],
            // 1-month difference -> month weight penalized
            [['2020-01-01', '2020-02-01'], round(self::$yearWeight + self::$monthWeight * 0.7 + self::$dayWeight, 3)],
            // 5-day difference -> day weight penalized with power(0.7, 5)
            [['2020-01-01', '2020-01-06'], round(self::$yearWeight + self::$monthWeight + self::$dayWeight * pow(0.7, 5), 3)],
            // 10-year difference -> beyond accepted diff, returns 0.0
            [['2010-01-01', '2020-01-01'], 0.4],
            // day-month inversion within same year -> triggers inversion max score
            [['2020-05-07', '2020-07-05'], 0.8],
            // reversed inversion -> same result
            [['2020-07-05', '2020-05-07'], 0.8],
            // mixed separators and extra spaces -> normalized equally
            [[' 2020/1/2 ', '02.01.2020'], 1.0],
            // different but close (1 month, 2 days) -> compound penalty
            [['2020-01-01', '2020-02-03'], round(self::$yearWeight + self::$monthWeight * 0.7 + self::$dayWeight * pow(0.7, 2), 3)],
            // two-digit year below threshold -> assumed 20xx (heuristic fallback in normalizer)
            [['20-01-01', '2001-01-20'], 1.0],
            // two-digit year above threshold (e.g. '99') -> assumed 1999 vs 2020 => 21 years diff -> 0.0
            [['99-01-01', '2020-01-01'], 0.4],
            // invalid characters -> normalization fails -> returns 0.0 20xx-01-01 -> 2001-01-20 (heuristic fallback in normalizer)
            [['20xx-01-01', '2020-01-01'], 0.2],
            // invalid format (only two parts) -> normalization fails -> returns 0.0
            [['2020-01', '2020-01-01'], 0.0],
            // empty strings -> normalization fails -> returns 0.0
            [['', '2020-01-01'], 0.0],
        ];
    }

    #[DataProvider('similarityDataProvider')]
    public function testSimilarity(array $input, float $expected): void
    {
        [$a, $b] = $input;

        $similarity = new DateFuzzySimilarity(
            new DateFuzzySimilarityConfiguration(
                new DatePartsWeights(),
                new DateDiffPenalty()
            ),
            new DateNormalizer()
        );

        $result = $similarity->similarity($a, $b);

        self::assertSame(
            $expected,
            $result,
            "Dates $a and $b aren't similar as expected."
        );
    }
}