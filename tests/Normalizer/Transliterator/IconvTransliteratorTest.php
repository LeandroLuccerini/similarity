<?php

declare(strict_types=1);

namespace Normalizer\Transliterator;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Szopen\Similarity\Normalizer\Transliterator\IconvTransliterator;

class IconvTransliteratorTest extends TestCase
{
    public static function transliterationDataProvider(): array
    {
        return [
            ['áéíóú', 'aeiou'], // basic accent stripping
            ['ç', 'c'], // cedilla to c
            ['ñ', 'n'], // spanish ñ to n
            ['œ', 'oe'], // ligature handled by iconv
            ['Łódź', 'Lodz'], // polish special letters transliteration
            ['über', 'uber'], // german umlaut to base vowel
            ['français', 'francais'], // franch word simplified
            ['', ''], // empty string unchanged
            ['12345', '12345'], // numbers unchanged
            ['@#$%', '@#$%'], // symbols unchanged
        ];
    }

    #[DataProvider('transliterationDataProvider')]
    public function testTransliteration(string $input, string $expected): void
    {
        $transliterator = new IconvTransliterator();
        $this->assertSame($expected, $transliterator->transliterate($input));
    }

}