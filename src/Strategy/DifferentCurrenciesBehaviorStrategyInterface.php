<?php

namespace Chetkov\Money\Strategy;

use Chetkov\Money\Money;

/**
 * Interface DifferentCurrenciesBehaviorStrategyInterface
 * @package Chetkov\Money\Strategy
 */
interface DifferentCurrenciesBehaviorStrategyInterface
{
    /**
     * @param Money $money
     * @param string $currency
     * @return Money
     */
    public function execute(Money $money, string $currency): Money;
}