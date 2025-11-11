<?php

declare(strict_types=1);

namespace Normalizer\Configuration;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;
use Szopen\Similarity\Configuration\DatePartsWeights;

#[Group("configuration")]
class DatePartsWeightsTest extends TestCase
{
    public function testWeights(): void
    {
        $w = new DatePartsWeights(0.3, 0.3, 0.4);
        $weights = $w->weights();
        $this->assertArrayHasKey("Y", $weights);
        $this->assertArrayHasKey("m", $weights);
        $this->assertArrayHasKey("d", $weights);

        $this->assertSame(
            [
                'Y' => 0.3,
                'm' => 0.3,
                'd' => 0.4,
            ],
            $weights
        );
    }
}