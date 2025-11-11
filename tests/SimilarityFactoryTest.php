<?php

declare(strict_types=1);

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;
use Szopen\Similarity\Configuration\DateDiffPenalty;
use Szopen\Similarity\Configuration\DateFuzzySimilarityConfiguration;
use Szopen\Similarity\Configuration\DatePartsWeights;
use Szopen\Similarity\DateFuzzySimilarity;
use Szopen\Similarity\SimilarityFactory;
use Szopen\Similarity\StringExactSimilarity;
use Szopen\Similarity\StringFuzzySimilarity;

#[Group("similarity")]
class SimilarityFactoryTest extends TestCase
{

    private SimilarityFactory $factory;

    public function testCreate(): void
    {
        $this->assertInstanceOf(
            StringExactSimilarity::class,
            $this->factory->create(SimilarityFactory::STRING_EXACT)
        );

        $this->assertInstanceOf(
            StringFuzzySimilarity::class,
            $this->factory->create(SimilarityFactory::STRING_FUZZY)
        );

        $this->assertInstanceOf(
            DateFuzzySimilarity::class,
            $this->factory->create(SimilarityFactory::DATE_FUZZY)
        );
    }

    public function testCreateRaiseExceptionDueToUnsupportedType(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Similarity type 'unsupported' is not supported");

        $this->factory->create('unsupported');
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->factory = new SimilarityFactory(
            new DateFuzzySimilarityConfiguration(
                new DatePartsWeights(),
                new DateDiffPenalty(),
            )
        );
    }
}