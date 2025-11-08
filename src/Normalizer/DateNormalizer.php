<?php

declare(strict_types=1);

namespace Szopen\Similarity\Normalizer;

final readonly class DateNormalizer implements Normalizer
{
    private const YEAR_MONTH_DAY_TEMPLATE = '/^\d{4}[-\/.]\d{1,2}[-\/.]\d{1,2}$/';
    private const DAY_MONTH_YEAR_TEMPLATE = '/^\d{1,2}[-\/.]\d{1,2}[-\/.]\d{2,4}$/';
    private const COMMON_DATE_SEPARATORS = '/[\/\-.]/';

    public function normalize(string $string): ?string
    {
        $date = trim($string);
        if ($date === '') {
            return null;
        }

        $parts = $this->parseAndGetDateParts($date);
        if (!is_array($parts)) {
            return null;
        }

        [$day, $month, $year] = $this->guessDayMonthAndYear($date, $parts);
        return sprintf(
            "%s-%s-%s",
            $this->fixTwoCharsYear($year),
            str_pad($month, 2, '0', STR_PAD_LEFT),
            str_pad($day, 2, '0', STR_PAD_LEFT)
        );
    }


    /**
     * @param string $date
     * @return list<string>|null
     */
    private function parseAndGetDateParts(string $date): ?array
    {
        $date = $this->removeNonNumericCharsAndNotAllowedSeparators($date);
        if (null === $date) {
            return null;
        }

        return $this->getThreeDateParts($date);
    }

    private function removeNonNumericCharsAndNotAllowedSeparators(string $date): ?string
    {
        return preg_replace('/[^0-9\-\/.]/', '', $date);
    }

    /**
     * @return list<string>|null
     */
    private function getThreeDateParts(string $date): ?array
    {
        /** @var list<string>|false $parts */
        $parts = preg_split(self::COMMON_DATE_SEPARATORS, $date);
        if (!is_array($parts)) {
            return null;
        }

        /** @var list<string> $parts */
        $parts = array_filter($parts, fn($p) => $p !== '');
        if (count($parts) !== 3) {
            return null;
        }

        return $parts;
    }

    /**
     * @param string $date
     * @param list<string> $parts
     * @return list<string>
     */
    private function guessDayMonthAndYear(string $date, array $parts): array
    {
        if (
            preg_match(self::YEAR_MONTH_DAY_TEMPLATE, $date)
        ) {
            if (intval($parts[1]) <= 12) {
                [$year, $month, $day] = $parts;
            } else {
                [$year, $day, $month] = $parts;
            }
        } elseif (
            preg_match(self::DAY_MONTH_YEAR_TEMPLATE, $date)
        ) {
            if (intval($parts[1]) <= 12) {
                [$day, $month, $year] = $parts;
            } else {
                [$month, $day, $year] = $parts;
            }
        } else {
            [$a, $b, $c] = $parts;
            if (strlen($a) === 4) {
                [$year, $month, $day] = [$a, $b, $c];
            } elseif (strlen($c) === 4) {
                [$day, $month, $year] = [$a, $b, $c];
            } else {
                // heuristic fallback
                [$day, $month, $year] = [$a, $b, $c];
            }
        }

        return [$day, $month, $year];
    }

    private function fixTwoCharsYear(string $year): string
    {
        if ($year && strlen($year) === 2) {
            $year = intval($year) >= 50 ? "19$year" : "20$year";
        }

        return $year;
    }
}
