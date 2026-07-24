<?php

namespace App\Support;

final class CryptoPriceFormatter
{
    public static function format(mixed $value, string $currency = '$'): string
    {
        if ($value === null || $value === '' || !is_numeric($value)) {
            return '-';
        }

        $price = (float) $value;

        if (!is_finite($price)) {
            return '-';
        }

        if ($price === 0.0) {
            return $currency.'0.00';
        }

        $absolute = abs($price);
        $decimals = match (true) {
            $absolute >= 1 => 2,
            $absolute >= 0.01 => 4,
            $absolute >= 0.0001 => 6,
            $absolute >= 0.000001 => 8,
            $absolute >= 0.00000001 => 10,
            default => 12,
        };

        $formatted = number_format($price, $decimals, '.', ',');
        $formatted = rtrim(rtrim($formatted, '0'), '.');

        return $currency.$formatted;
    }
}
