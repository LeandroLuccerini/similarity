<?php

declare(strict_types=1);

namespace Szopen\Similarity\Configuration;

readonly class DatePartsWeights
{
    public function __construct(
        private float $yearWeight = 0.5,
        private float $monthWeight = 0.25,
        private float $dayWeight = 0.25,
    ) {
    }

    /**
     * @return array<string, float>
     */
    public function weights(): array
    {
        return [
            'Y' => $this->yearWeight,
            'm' => $this->monthWeight,
            'd' => $this->dayWeight
        ];
    }
}