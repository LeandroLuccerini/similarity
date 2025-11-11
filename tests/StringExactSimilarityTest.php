<?php

declare(strict_types=1);

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;
use Szopen\Similarity\StringExactSimilarity;

#[Group("similarity")]
class StringExactSimilarityTest extends TestCase
{
    private StringExactSimilarity $similarity;

    public static function similarityDataProvider(): array
    {
        return [
            ['Hello', 'Hello', 1.0],
            ['hello', 'HELLO', 1.0],
            ['Hello, world!', 'hello world', 0.0],
            ['Café', 'Cafe', 0.0],
            ['Hello', 'Hi', 0.0],
            ['Foo', 'Bar', 0.0],
            ['M', 'm', 1.0],
            ['F', 'f', 1.0],
        ];
    }

    public static function similarityExceptionDataProvider(): array
    {
        return [
            ['', 'hello'],
            ['hello', ''],
            ['', ''],
        ];
    }

    #[DataProvider('similarityDataProvider')]
    public function testSimilarity(string $a, string $b, float $expected): void
    {
        self::assertSame(
            $expected,
            $this->similarity->similarity($a, $b)
        );
    }

    #[DataProvider('similarityExceptionDataProvider')]
    public function testSimilarityRaisesExceptionDuToEmptyValue(string $a, string $b): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            sprintf(
                "Both arguments must be not empty, found a = '%s' b = '%s'",
                $a,
                $b
            )
        );
        $this->similarity->similarity($a, $b);
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->similarity = new StringExactSimilarity();
    }

}