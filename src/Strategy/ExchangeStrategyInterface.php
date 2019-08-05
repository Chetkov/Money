<?php

namespace Chetkov\Money\Strategy;

use Chetkov\Money\Exception\ExchangeRateWasNotFoundException;
use Chetkov\Money\Money;

/**
 * Interface ExchangerInterface
 * @package Chetkov\Money\Strategy
 */
interface ExchangeStrategyInterface
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