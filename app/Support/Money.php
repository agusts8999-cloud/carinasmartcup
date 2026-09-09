<?php

namespace App\Support;

class Money
{
    /**
     * Parse a monetary amount from Indonesian (IDR) or mixed locale strings.
     *
     * Examples:
     * - "29.000" => 29000 (dot as thousands separator)
     * - "15,000" => 15000 (comma as thousands separator)
     * - "1.234.567,89" => 1234567.89
     * - "1,234,567.89" => 1234567.89
     */
    public static function parseIdr(mixed $value): float
    {
        if (is_int($value) || is_float($value)) {
            return (float) $value;
        }

        $raw = trim((string) $value);

        if ($raw === '') {
            return 0.0;
        }

        $raw = preg_replace('/[^\d.,-]/', '', $raw) ?? '0';

        if ($raw === '' || $raw === '-') {
            return 0.0;
        }

        $hasComma = str_contains($raw, ',');
        $hasDot = str_contains($raw, '.');

        if ($hasComma && $hasDot) {
            $lastComma = strrpos($raw, ',');
            $lastDot = strrpos($raw, '.');

            if ($lastComma !== false && ($lastDot === false || $lastComma > $lastDot)) {
                // Indonesian: 1.234.567,89
                $raw = str_replace('.', '', $raw);
                $raw = str_replace(',', '.', $raw);
            } else {
                // US: 1,234,567.89
                $raw = str_replace(',', '', $raw);
            }
        } elseif ($hasDot) {
            if (preg_match('/^\d{1,3}(\.\d{3})+$/', $raw) === 1) {
                $raw = str_replace('.', '', $raw);
            }
        } elseif ($hasComma) {
            if (preg_match('/^\d{1,3}(,\d{3})+$/', $raw) === 1) {
                $raw = str_replace(',', '', $raw);
            } else {
                $raw = str_replace(',', '.', $raw);
            }
        }

        return (float) $raw;
    }

    public static function formatIdr(float|int|null $amount): string
    {
        if ($amount === null) {
            return '';
        }

        return number_format((float) $amount, 0, ',', '.');
    }
}
