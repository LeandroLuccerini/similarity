<?php

declare(strict_types=1);

namespace Szopen\Similarity\Normalizer;

final class DateNormalizer implements Normalizer
{
    private const YYYY_MM_DD_TEMPLATE = '/^\d{4}[-\/.]\d{1,2}[-\/.]\d{1,2}$/';
    private const DD_MM_YYYY_TEMPLATE = '/^\d{1,2}[-\/.]\d{1,2}[-\/.]\d{2,4}$/';
    private const COMMON_DATE_SEPARATORS = '/[\/\-.]/';

    public function __construct(private ?int $twoDigitsYearThreshold = null)
    {
        if (null === $this->twoDigitsYearThreshold) {
            $this->twoDigitsYearThreshold = intval(date('y'));
        }
    }

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

        [$year, $month, $day] = $this->guessYearMonthAndDay($date, $parts);
        return sprintf(
            "%s-%s-%s",
            $this->fixTwoDigitsYear($year),
            str_pad($month, 2, '0', STR_PAD_LEFT),
            str_pad($day, 2, '0', STR_PAD_LEFT)
        );
    }

    /**
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
    private function guessYearMonthAndDay(string $date, array $parts): array
    {
        if (preg_match(self::YYYY_MM_DD_TEMPLATE, $date)) {
            return $this->getPartsFromYYYYMMDDTemplate($parts);
        } elseif (preg_match(self::DD_MM_YYYY_TEMPLATE, $date)) {
            return $this->getPartsFromDDMMYYYYTemplate($parts);
        } else {
            [$a, , $c] = $parts;
            if (strlen($a) === 4) {
                return $this->getPartsFromYYYYMMDDTemplate($parts);
            } elseif (strlen($c) === 4) {
                return $this->getPartsFromDDMMYYYYTemplate($parts);
            } else {
                // heuristic fallback
                return array_reverse($parts); // [year, month, day]
            }
        }
    }

    private function getPartsFromYYYYMMDDTemplate(array $parts): array
    {
        if ($this->couldBeAMonthValue(intval($parts[1]))) {
            [$year, $month, $day] = $parts;
        } else {
            [$year, $day, $month] = $parts;
        }

        return [$year, $month, $day];
    }

    private function couldBeAMonthValue(int $month): bool
    {
        return $month <= 12;
    }

    private function getPartsFromDDMMYYYYTemplate(array $parts): array
    {
        if ($this->couldBeAMonthValue(intval($parts[1]))) {
            [$day, $month, $year] = $parts;
        } else {
            [$month, $day, $year] = $parts;
        }

        return [$year, $month, $day];
    }

    private function fixTwoDigitsYear(string $year): string
    {
        if ($year && strlen($year) === 2) {
            $year = intval($year) >= $this->twoDigitsYearThreshold ? "19$year" : "20$year";
        }

        return $year;
    }
}
