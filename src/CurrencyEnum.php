<?php

namespace Chetkov\Money;

/**
 * Class CurrencyEnum
 * @package Chetkov\Money
 * TODO: Нужно-ли это перечисление?
 */
class CurrencyEnum
{
    public const RUB = 'RUB';
    public const AUD = 'AUD';
    public const AZN = 'AZN';
    public const GBP = 'GBP';
    public const AMD = 'AMD';
    public const BYN = 'BYN';
    public const BGN = 'BGN';
    public const BRL = 'BRL';
    public const HUF = 'HUF';
    public const HKD = 'HKD';
    public const DKK = 'DKK';
    public const USD = 'USD';
    public const EUR = 'EUR';
    public const INR = 'INR';
    public const KZT = 'KZT';
    public const CAD = 'CAD';
    public const KGS = 'KGS';
    public const CNY = 'CNY';
    public const MDL = 'MDL';
    public const NOK = 'NOK';
    public const PLN = 'PLN';
    public const RON = 'RON';
    public const XDR = 'XDR';
    public const SGD = 'SGD';
    public const TJS = 'TJS';
    public const TRY = 'TRY';
    public const TMT = 'TMT';
    public const UZS = 'UZS';
    public const UAH = 'UAH';
    public const CZK = 'CZK';
    public const SEK = 'SEK';
    public const CHF = 'CHF';
    public const ZAR = 'ZAR';
    public const KRW = 'KRW';
    public const JPY = 'JPY';

    /**
     * @param string $sellingCurrencyCode
     * @param string $purchasedCurrencyCode
     * @return string
     * TODO: Нужно найти более подходящее место
     */
    public static function getCurrencyPairCode(string $sellingCurrencyCode, string $purchasedCurrencyCode): string
    {
        return "{$sellingCurrencyCode}-{$purchasedCurrencyCode}";
    }
}
