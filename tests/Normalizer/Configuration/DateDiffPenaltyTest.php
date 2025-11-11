<?php

declare(strict_types=1);

namespace Normalizer\Configuration;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;
use Szopen\Similarity\Configuration\DateDiffPenalty;

#[Group("configuration")]
class DateDiffPenaltyTest extends TestCase
{
    public static function calculateDataProvider(): array
    {
        return [
            // same date parts -> returns full weight
            [[1.0, 2020, 2020], 1.0],
            // diff of 1 -> weight * penalty^1 = 1.0 * 0.7
            [[1.0, 2020, 2021], 0.7],
            // diff of 2 -> weight * penalty^2 = 1.0 * 0.49
            [[1.0, 2020, 2022], 0.49],
            // diff of 3 -> weight * penalty^3 = 1.0 * 0.343
            [[1.0, 2020, 2023], 0.343],
            // diff of 5 -> weight * penalty^5 = 1.0 * 0.16807
            [[1.0, 2020, 2025], 0.168],
            // diff greater than max accepted -> returns 0.0
            [[1.0, 2020, 2030], 0.0],
            // custom weight -> same date parts should return that weight
            [[2.5, 1990, 1990], 2.5],
            // custom weight and diff of 2 -> 2.5 * 0.49
            [[2.5, 1990, 1992], 1.225],
            // reversed order should behave the same
            [[1.0, 2025, 2020], 0.168],
            // large diff with custom parameters -> should still return 0.0
            [[5.0, 2000, 2010], 0.0],
        ];
    }

    #[DataProvider('calculateDataProvider')]
    public function testCalculate(array $input, float $expected): void
    {
        [$weight, $datePartA, $datePartB] = $input;
        $penalty = new DateDiffPenalty();
        $result = $penalty->calculate($weight, $datePartA, $datePartB);

        self::assertSame($expected, $result);
    }
}