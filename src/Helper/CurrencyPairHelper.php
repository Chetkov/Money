<?php

namespace Chetkov\Money\Helper;

/**
 * Class CurrencyPairHelper
 * @package Chetkov\Money\Helper
 */
class CurrencyPairHelper
{
    private const DELIMITER = '-';

    /**
     * @param string $sellingCurrency
     * @param string $purchasedCurrency
     * @return string
     */
    public static function implode(string $sellingCurrency, string $purchasedCurrency): string
    {
        return implode(self::DELIMITER, [$sellingCurrency, $purchasedCurrency]);
    }

    /**
     * @param string $pairCode
     * @return string[] [$sellingCurrency, $purchasedCurrency]
     */
    public static function explode(string $pairCode): array
    {
        return explode(self::DELIMITER, $pairCode);
    }

    /**
     * @param string $pairCode
     * @return string
     */
    public static function reverse(string $pairCode): string
    {
        return self::implode(...array_reverse(self::explode($pairCode)));
    }
}
