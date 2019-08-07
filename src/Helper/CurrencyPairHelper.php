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
}
