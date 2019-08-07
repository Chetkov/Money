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
     * @param string $sellingCurrencyCode
     * @param string $purchasedCurrencyCode
     * @return string
     */
    public static function implode(string $sellingCurrencyCode, string $purchasedCurrencyCode): string
    {
        return implode(self::DELIMITER, [$sellingCurrencyCode, $purchasedCurrencyCode]);
    }

    /**
     * @param string $pairCode
     * @return string[] [$sellingCurrencyCode, $purchasedCurrencyCode]
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
