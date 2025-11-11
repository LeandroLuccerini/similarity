<?php

declare(strict_types=1);

namespace Szopen\Similarity;

use InvalidArgumentException;

final readonly class StringExactSimilarity implements Similarity
{
    /**
     * @inheritDoc
     */
    public function similarity(string $a, string $b): float
    {
        $a = trim($a);
        $b = trim($b);
        $this->assertValidArguments($a, $b);

        $normalizedA = $this->multibyteLowercase($a);
        $normalizedB = $this->multibyteLowercase($b);

        if ($normalizedA === $normalizedB) {
            return 1.0;
        }

        return 0.0;
    }

    private function assertValidArguments(
        string $a,
        string $b
    ): void {
        if (empty($a) || empty($b)) {
            throw new InvalidArgumentException(
                sprintf("Both arguments must be not empty, found a = '%s' b = '%s'", $a, $b)
            );
        }
    }

    private function multibyteLowercase(string $s): string
    {
        return mb_strtolower($s, 'UTF-8');
    }
}
