<?php

declare(strict_types=1);

namespace Normalizer;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;
use Szopen\Similarity\Normalizer\DateNormalizer;

#[Group("normalizer")]
class DateNormalizerTest extends TestCase
{
    public static function normalizeProvider(): array
    {
        return [
            ['1979-03-12', '1979-03-12'],
            ['1979/03/12', '1979-03-12'],
            ['12/03/1979', '1979-03-12'],
            ['12.03.1979', '1979-03-12'],
            ['12/03/79', '1979-03-12'],
            ['04/05/23', '2023-05-04'],
            ['12-03.1979', '1979-03-12'],
            ['   12 / 03 / 1979  ', '1979-03-12'],
            ['1979-3-12', '1979-03-12'],
            ['9/3/1979', '1979-03-09'],
            ['1979.3.12', '1979-03-12'],
            ['12.3.1979', '1979-03-12'],
            ['10/31/1979', '1979-10-31'],
            ['1979-31-10', '1979-10-31'],
            ['1979-03', null],
            ['', null],
            ['abc', null],
        ];
    }

    public static function ambiguousProvider(): array
    {
        return [
            ['03/12/1979', '1979-12-03'], // Possible day month inversion, algorithm opts for DD/MM/YYYY
            ['12/03/1979', '1979-03-12'], // Possible day month inversion, algorithm opts for DD/MM/YYYY
            ['07/02/05', '2005-02-07'], // Two digit year 2005 vs 1995: algorithm opts for 2005
            ['2-1-1979', '1979-01-02'], // Small ambiguous numbers but still DD_MM_YYYY_TEMPLATE coherent
            ['15-7.1989', '1989-07-15'], // Mixed separator format
            ['01--10/1990', '1990-10-01'], // Mixed separator format but still parsable
            ['1985-04-11', '1985-04-11'], // YYYY_MM_DD_TEMPLATE format
            ['1999.3.12', '1999-03-12'], // Heuristic fallback, no pattern matched
            ['01--02--00', '2000-02-01'], // Double separator
            ['12/1979', null], // Partial values
            ['Data: 12-03-1979!', '1979-03-12'], // Extra noise, still parsable
        ];
    }

    #[DataProvider('normalizeProvider')]
    public function testNormalize(string $input, ?string $expected): void
    {
        $normalizer = new DateNormalizer(50);
        $this->assertSame(
            $expected,
            $normalizer->normalize($input),
            "Failed normalizing '$input'"
        );
    }

    public function testNormalizeWithDifferentThresholds(): void
    {
        $normalizer = new DateNormalizer(50);
        $this->assertSame(
            "2001-01-01",
            $normalizer->normalize("01/01/01"),
            "Wrong guess with threshold '50' for '01/01/01'"
        );

        $normalizer = new DateNormalizer(50);
        $this->assertSame(
            "1950-01-01",
            $normalizer->normalize("01/01/50"),
            "Wrong guess with threshold '50' for '01/01/50'"
        );
    }

    #[DataProvider('ambiguousProvider')]
    public function testAmbiguousNormalization(string $input, ?string $expected): void
    {
        $normalizer = new DateNormalizer(50);
        $this->assertSame(
            $expected,
            $normalizer->normalize($input),
            "Ambiguous case failed for '$input'"
        );
    }
}