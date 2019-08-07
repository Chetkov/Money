<?php

namespace Chetkov\Money\Exchanger;

use Chetkov\Money\Exception\ExchangeRateWasNotFoundException;
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
        $sellingCurrency = $money->getCurrency();
        $purchasedCurrency = $currency;
        return $this->calculateExchangeAmount($money->getAmount(), $sellingCurrency, $purchasedCurrency, $exchangeRates);
    }
}
