<?php

declare(strict_types=1);

namespace Normalizer;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Szopen\Similarity\Normalizer\DateNormalizer;

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
}