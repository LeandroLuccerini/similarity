<?php

declare(strict_types=1);

namespace Tests\Szopen\Similarity;

use InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Szopen\Similarity\FuzzySimilarity;
use Szopen\Similarity\Normalizer\StringNormalizer;
use Szopen\Similarity\Normalizer\TransliteratorFactory;


class FuzzySimilarityTest extends TestCase
{
    private FuzzySimilarity $fuzzy;

    public static function similarityDataProvider(): array
    {
        return [
            [1.0, 'Test', 'Test'],
            [1.0, 'Test ', ' test'],
            [1.0, 'H e l l o!', 'hello'],
            [1.0, 'ciao, mondo!', 'Ciao mondo'],
            [0.8, 'house', 'mouse'],
            [0.833, 'color', 'colour'],
            [0.571, 'kitten', 'sitting'],
            [1.0, 'a', 'a'],
            [0, 'a', 'b'],
            [0.5, 'ab', 'ac'],
            [1.0, 'café', 'cafe'],
            [1.0, 'résumé', 'resume'],
            [1.0, 'français', 'francais'],
            [1.0, 'HELLO', 'hello'],
            [1.0, 'Word', 'word'],
            [1.0, 'A.B,C;D!', 'ABCD'],
            [1.0, 'A B-C_D', 'abcd'],
            [1.0, 'ñandú', 'nandu'],
            [1.0, 'Gödel', 'Godel'],
            [0.2, 'apple', 'pear'],
            [0.0, 'abc', 'xyz'],
            [0.0, 'abc', 'xyz'],
        ];
    }

    public static function spanishSimilarityDataProvider(): array
    {
        return [
            [1.0, 'niño', 'nino'],
            [1.0, 'corazón', 'corazon'],
            [1.0, 'acción', 'accion'],
            [1.0, 'Canción', 'cancion'],
            [1.0, 'mañana', 'manana'],
            [1.0, 'hablar', 'hablár'],
            [0.667, 'comer', 'correr'],
            [0.5, 'hola', 'halo'],
            [1.0, '¡Hola mundo!', 'hola mundo'],
            [1.0, 'El niño juega.', 'El nino juega'],
            [0.2, 'gato', 'perro'],
        ];
    }

    public static function frenchSimilarityDataProvider(): array
    {
        return [
            [1.0, 'français', 'francais'],
            [1.0, 'école', 'ecole'],
            [1.0, 'à bientôt', 'a bientot'],
            [1.0, 'garçon', 'garcon'],
            [1.0, 'tête', 'tete'],
            [1.0, "l'homme", "lhomme"],
            [1.0, "j'aime", "jaime"],
            [1.0, "c'est", "cest"],
            [0.714, 'bonjour', 'bonsoir'],
            [0.8, 'merci', 'mercu'],
            [0.5, 'chat', 'chou'],
            [1.0, 'ÉTÉ', 'été'],
            [0.143, 'fromage', 'voiture'],
        ];
    }

    public static function chineseSimilarityDataProvider(): array
    {
        return [
            [1.0, '你好', '你 好'],
            [1.0, '再见', '再 見'],
            [1.0, '谢谢', '謝謝'],
            [1.0, '中国', '中國'],
            [1.0, '你好', 'nihao'],
            [1.0, '谢谢', 'xiexie'],
            [1.0, '再见', 'zaijian'],
            [1.0, '中国', 'zhongguo'],
            [1.0, '北京', 'beijing'],
            [1.0, '上海', 'shanghai'],
            [1.0, '你好', 'nǐ hǎo'],
            [1.0, '谢谢', 'xiè xiè'],
            [1.0, '北京', 'běi jīng'],
            [1.0, '中国', 'zhōng guó'],
            [1.0, '北京', '北景'],
            [0.875, '上海', '上好'],
            [1.0, '你好世界', '你好，世界！'],
            [1.0, '谢谢你朋友', '謝謝你，朋友。'],
            [0.286, '你好', '再见'],
            [0.375, '中国', '美国'],
        ];
    }

    public static function exceptionsCaseDataProvider(): array
    {
        return [
            ["", "hello", "String '' is not a normalizable string."],
            ["hello", "", "String '' is not a normalizable string."],
            ["", "", "String '' is not a normalizable string."],
        ];
    }

    public static function idNumberSimilarityDataProvider()
    {
        return [
            [1.0, 'RSSMRA85T10A562S', 'RSSMRA85T10A562S'],
            [1.0, 'rssmra85t10a562s', 'RSSMRA85T10A562S'],
            [1.0, 'RSSMRA85T10A562S', 'R S S M R A 85 T10 A 562 S'],
            [0.938, 'RSSMRA85T10A562S', 'RSSMRA85T10A562R'],
            [0.938, 'RSSMRA85T10A562S', 'RSSMRA85T10A562'],
            [0.938, 'RSSMRA85T10A562S', 'RSSMRA85T10B562S'],
            [0.938, 'VRDLGI90C10H501T', 'VRDLGU90C10H501T'],
            [0.938, 'BNCLNZ88M24F205T', 'BNCLNZ88M25F205T'],
            [0.938, 'PLLMRA80A01F205X', 'PLLMRA90A01F205X'],
            [1.0, 'CA1234567', 'CA 1234567'],
            [1.0, 'AB-9876543', 'AB9876543'],
            [0.889, 'CA1234567', 'CA1234568'],
            [0.889, 'AA0001112', 'AA0001119'],
            [0.875, 'RSSMRA85T10A562S', 'RSSMRA8510A562'],
            [0.188, 'CA1234567', 'RSSMRA85T10A562S'],
            [1.0, 'RSS MRA 85T10 A562S', 'rssmra85t10a562s'],
            [1.0, 'R.S.S.M.R.A.85T10A562S', 'RSSMRA85T10A562S'],
            [1.0, 'ID-IT-2023-AB1234', 'id it 2023 ab1234'],
            [0.929, 'ID-IT-2023-AB1234', 'ID-IT-2023-AB1244'],
            [0.125, 'XYZ123456', 'RSSMRA85T10A562S'],
            [0.0, 'AA1111111', 'BB9999999'],
        ];
    }

    #[DataProvider("similarityDataProvider")]
    public function testSimilarity(float $expectedSimilarity, string $stringA, string $stringB): void
    {
        $this->assertEquals(
            $expectedSimilarity,
            $this->fuzzy->similarity($stringA, $stringB)
        );
    }

    #[DataProvider("spanishSimilarityDataProvider")]
    public function testSpanishSimilarity(float $expectedSimilarity, string $stringA, string $stringB): void
    {
        $this->assertEquals(
            $expectedSimilarity,
            $this->fuzzy->similarity($stringA, $stringB)
        );
    }

    #[DataProvider("frenchSimilarityDataProvider")]
    public function testFrenchSimilarity(float $expectedSimilarity, string $stringA, string $stringB): void
    {
        $this->assertEquals(
            $expectedSimilarity,
            $this->fuzzy->similarity($stringA, $stringB)
        );
    }

    #[DataProvider("chineseSimilarityDataProvider")]
    public function testChineseSimilarity(float $expectedSimilarity, string $stringA, string $stringB): void
    {
        $this->assertEquals(
            $expectedSimilarity,
            $this->fuzzy->similarity($stringA, $stringB)
        );
    }

    #[DataProvider("idNumberSimilarityDataProvider")]
    public function testIdNumberSimilarity(float $expectedSimilarity, string $stringA, string $stringB): void
    {
        $this->assertEquals(
            $expectedSimilarity,
            $this->fuzzy->similarity($stringA, $stringB)
        );
    }

    #[DataProvider("exceptionsCaseDataProvider")]
    public function testSimilarityRaisesExceptionDueToEmptyString(
        string $stringA,
        string $stringB,
        string $exceptionMessage
    ): void {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage($exceptionMessage);
        $this->fuzzy->similarity($stringA, $stringB);
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->fuzzy = new FuzzySimilarity(
            new StringNormalizer(
                new TransliteratorFactory()
            )
        );
    }
}