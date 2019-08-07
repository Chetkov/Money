<?php

namespace Chetkov\Money\Exchange;

use Chetkov\Money\CurrencyEnum;
use Chetkov\Money\Exception\ExchangeRateWasNotFoundException;
use Chetkov\Money\Money;

/**
 * Class SimpleExchanger
 * @package Chetkov\Money\Exchange
 */
class SimpleExchanger extends AbstractExchanger
{
    /**
     * @param Money $money
     * @param string $currency
     * @param array $exchangeRates
     * @return float
     * @throws ExchangeRateWasNotFoundException
     */
    protected function doExchange(Money $money, string $currency, array $exchangeRates): float
    {
        $currencyPair = CurrencyEnum::getCurrencyPairCode($money->getCurrency(), $currency);
        $reversePair = CurrencyEnum::getCurrencyPairCode($currency, $money->getCurrency());
        switch (true) {
            case isset($exchangeRates[$currencyPair]):
                $exchangedAmount = $money->getAmount() * $exchangeRates[$currencyPair];
                break;
            case isset($exchangeRates[$reversePair]):
                $exchangedAmount = $money->getAmount() / $exchangeRates[$reversePair];
                break;
            default:
                throw new ExchangeRateWasNotFoundException($currencyPair);
        }
        return $exchangedAmount;
    }
}
