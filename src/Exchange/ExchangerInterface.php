<?php

namespace Chetkov\Money\Exchange;

use Chetkov\Money\Exception\ExchangeRateWasNotFoundException;
use Chetkov\Money\Money;

/**
 * Interface ExchangerInterface
 * @package Chetkov\Money\Exchange
 */
interface ExchangerInterface
{
    /**
     * @param Money $money
     * @param string $currency
     * @param int $roundingPrecision
     * @return Money
     * @throws ExchangeRateWasNotFoundException
     */
    public function exchange(Money $money, string $currency, int $roundingPrecision = 2): Money;
}