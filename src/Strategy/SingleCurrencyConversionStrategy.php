<?php

namespace Chetkov\Money\Strategy;

use Chetkov\Money\Exception\ExchangeRateWasNotFoundException;
use Chetkov\Money\Exception\UnsupportedStrategyException;
use Chetkov\Money\Exchanger;
use Chetkov\Money\Money;

/**
 * Class SingleCurrencyConversionStrategy
 * @package Chetkov\Money\Strategy
 */
class SingleCurrencyConversionStrategy implements DifferentCurrenciesBehaviorStrategyInterface
{
    /** @var Exchanger */
    private $exchanger;

    /**
     * SingleCurrencyConversionStrategy constructor.
     * @param Exchanger $exchanger
     */
    public function __construct(Exchanger $exchanger)
    {
        $this->exchanger = $exchanger;
    }

    /**
     * @param Money $money
     * @param string $currency
     * @return Money
     * @throws ExchangeRateWasNotFoundException
     * @throws UnsupportedStrategyException
     */
    public function execute(Money $money, string $currency): Money
    {
        if ($money->getCurrency() !== $currency) {
            return $this->exchanger->exchange($money, $currency);
        }
        return $money;
    }
}
