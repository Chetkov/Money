<?php

namespace Chetkov\Money;

use Chetkov\Money\Exception\ExchangeRateWasNotFoundException;

/**
 * Interface ExchangerInterface
 * @package Chetkov\Money
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