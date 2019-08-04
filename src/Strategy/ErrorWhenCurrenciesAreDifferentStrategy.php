<?php

namespace Chetkov\Money\Strategy;

use Chetkov\Money\Exception\OperationWithDifferentCurrenciesException;
use Chetkov\Money\Money;

/**
 * Class ErrorWhenCurrenciesAreDifferentStrategy
 * @package Chetkov\Money\Strategy
 */
class ErrorWhenCurrenciesAreDifferentStrategy implements DifferentCurrenciesBehaviorStrategyInterface
{
    /**
     * @param Money $money
     * @param string $currency
     * @return Money
     * @throws OperationWithDifferentCurrenciesException
     */
    public function execute(Money $money, string $currency): Money
    {
        if ($money->getCurrency() !== $currency) {
            throw new OperationWithDifferentCurrenciesException();
        }
        return $money;
    }
}
