<?php

declare(strict_types=1);

namespace Normalizer\Transliterator;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Szopen\Similarity\Normalizer\Transliterator\IntlTransliterator;

class IntlTransliteratorTest extends TestCase
{
    public static function transliterationDataProvider(): array
    {
        return [
            ['áéíóú', 'aeiou'], // simple accented vowels to ascii
            ['ç', 'c'], // cedilla transliteration
            ['ñ', 'n'], // spanish ñ to n
            ['œ', 'oe'], // ligature oe to oe
            ['Łódź', 'Lodz'], // polish characters normalization
            ['über', 'uber'], // german umlaut transliteration
            ['français', 'francais'], // french accents normalized
            ['Ελλάδα', 'Ellada'], // greek to latin
            ['東京', 'dong jing'], // chinese kanji to latin
            ['中文', 'zhong wen'], // chinese hanzi to pinyin
            ['', ''], // empty string should remain empty
            ['12345', '12345'], // numeric string unchanged
            ['@@@###', '@@@###'], // symbols unchanged
        ];
    }

    #[DataProvider('transliterationDataProvider')]
    public function testTransliteration(string $input, string $expected): void
    {
        $transliterator = new IntlTransliterator();
        $this->assertSame($expected, $transliterator->transliterate($input));
    }

}