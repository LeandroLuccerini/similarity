<?php

declare(strict_types=1);

namespace Tests\Szopen\Similarity\Normalizer;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;
use Szopen\Similarity\Normalizer\StringNormalizer;

#[Group("normalizer")]
class StringNormalizerTest extends TestCase
{
    public static function normalizeDataProvider(): array
    {
        return [
            // simple lowercase
            ['ciao mondo', 'ciaomondo'],
            // UPPERCASE → lowercase
            ['CIAO MONDO', 'ciaomondo'],
            // trims spaces
            ['   ciao mondo   ', 'ciaomondo'],
            // multiple spaces removed
            ['ciao    mondo   bello', 'ciaomondobello'],
            // removed interpunctuation
            ['ciao, mondo! bello?', 'ciaomondobello'],
            // accents and transliteration
            ['città naïve élève über', 'cittanaiveeleveuber'],
            // combined unicode (é like e + accent combining)
            ["e\u{0301}cole", 'ecole'],
            // special chars
            ['@hello#world$123', 'helloworld123'],
            // just spaces
            ['     ', null],
            // empty string
            ['', null],
            // complex accents and symbols
            ["  Héllò---Wörld!!  ", 'helloworld'],
            // just numbers
            ['12345', '12345'],
            // alphanumeric
            ['ABC123xyz', 'abc123xyz'],
            // emoji and ASCII simbols
            ['ciao 🌍!', 'ciao'],
            // accents ad numbers
            ['Café123', 'cafe123'],
            // tab and newline removed
            ["ciao\tmondo\nbello", 'ciaomondobello'],
            // just symbols → null
            ['!@#$%^&*()', null],
        ];
    }

    public static function chineseDataProvider(): array
    {
        return [
            ['你好', 'nihao'],
            ['中国', 'zhongguo'],
            ['北京', 'beijing'],
            ['上海', 'shanghai'],
            ['广州', 'guangzhou'],
            ['谢谢', 'xiexie'],
            ['再见', 'zaijian'],
            ['早上好', 'zaoshanghao'],
            ['晚上好', 'wanshanghao'],
            ['我爱你', 'woaini'],
            ['你好世界', 'nihaoshijie'],
            ['你好，世界！', 'nihaoshijie'],
            ['  你好 @ 世界  ', 'nihaoshijie'],
            ['中国abc', 'zhongguoabc'],
            ['第123章', 'di123zhang'],
            ['你好🌍', 'nihao'],
            ['❤️我爱你', 'woaini'],
            ['※中国※', 'zhongguo'],
            ['  早 上   好  ', 'zaoshanghao'],
            ['💬🎉', null],
            ["好\u{0301}", 'hao'],
        ];
    }

    public static function spanishDataProvider(): array
    {
        return [
            ['acción', 'accion'],
            ['camión', 'camion'],
            ['teléfono', 'telefono'],
            ['año', 'ano'],
            ['niño', 'nino'],
            ['mañana', 'manana'],
            ['pingüino', 'pinguino'],
            ['corazón', 'corazon'],
            [' ¡Hola, mundo! ', 'holamundo'],
            [' ¿Qué tal? ', 'quetal'],
            ['España', 'espana'],
            ['Señor López', 'senorlopez'],
            ["nin\u{0303}o", 'nino'],
        ];
    }

    public static function frenchDataProvider(): array
    {
        return [
            ['école', 'ecole'],
            ['français', 'francais'],
            ['garçon', 'garcon'],
            ['maïs', 'mais'],
            ['Noël', 'noel'],
            ['où est la bibliothèque', 'ouestlabibliotheque'],
            ['ça va bien', 'cavabien'],
            ["l'élève", 'leleve'],
            ["aujourd’hui", 'aujourdhui'],
            ['cœur', 'coeur'],
            ['œuvre', 'oeuvre'],
            ['sœur', 'soeur'],
            ['   Très   bien!  ', 'tresbien'],
            ['Bonjour—monde!', 'bonjourmonde'],
            ['«Salut!»', 'salut'],
            ['École123', 'ecole123'],
            ['naïve café', 'naivecafe'],
        ];
    }

    #[DataProvider("normalizeDataProvider")]
    #[DataProvider("spanishDataProvider")]
    #[DataProvider("frenchDataProvider")]
    #[DataProvider("chineseDataProvider")]
    public function testNormalizer(string $input, ?string $output): void
    {
        $n = new StringNormalizer();
        $this->assertEquals($output, $n->normalize($input));
    }

    #[DataProvider("normalizeDataProvider")]
    #[DataProvider("spanishDataProvider")]
    #[DataProvider("frenchDataProvider")]
    public function testNormalizerWithoutIntl(string $input, ?string $output): void
    {
        $n = new StringNormalizer();
        $this->assertEquals($output, $n->normalize($input));
    }
}