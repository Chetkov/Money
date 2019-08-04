<?php

namespace Chetkov\Money\Strategy;

use Chetkov\Money\Money;

/**
 * Interface CurrencyConversationStrategyInterface
 * @package Chetkov\Money\Strategy
 */
interface CurrencyConversationStrategyInterface
{
    /**
     * @param Money $other
     * @param Money $current
     * @return Money
     */
    public function convert(Money $other, Money $current): Money;
}