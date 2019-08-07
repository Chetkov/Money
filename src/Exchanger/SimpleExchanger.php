<?php

namespace Chetkov\Money\Exchanger;

use Chetkov\Money\Exception\ExchangeRateWasNotFoundException;
use Chetkov\Money\Helper\CurrencyPairHelper;
use Chetkov\Money\Money;

/**
 * Class SimpleExchanger
 * @package Chetkov\Money\Exchanger
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
        $currencyPair = CurrencyPairHelper::implode($money->getCurrency(), $currency);
        $reversePair = CurrencyPairHelper::implode($currency, $money->getCurrency());
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
