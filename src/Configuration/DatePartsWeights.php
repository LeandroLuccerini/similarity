<?php

declare(strict_types=1);

namespace Szopen\Similarity\Configuration;

readonly class DatePartsWeights
{
    public function __construct(
        private float $yearWeight = 0.60,
        private float $monthWeight = 0.20,
        private float $dayWeight = 0.20,
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
