<?php

namespace Chetkov\Money;

use Chetkov\Money\Exception\ExchangeRateWasNotFoundException;

/**
 * Class Exchanger
 * @package Chetkov\Money
 */
class Exchanger
{
    /** @var Exchanger */
    private static $instance;

    /** @var float[] */
    private $exchangeRates;

    /**
     * Exchanger constructor.
     * @param float[] $exchangeRates Example: ['USD-RUB' => 66.34]
     */
    public function __construct(array $exchangeRates)
    {
        $this->exchangeRates = $exchangeRates;
    }

    /**
     * @param float[] $exchangeRates Example: ['USD-RUB' => 66.34]
     * @return Exchanger
     */
    public static function getInstance(array $exchangeRates = []): self
    {
        if (null === self::$instance) {
            self::$instance = new self($exchangeRates);
        }
        return self::$instance;
    }

    /**
     * @param string $currencyPair
     * @param float $exchangeRate
     * @return Exchanger
     */
    public function addCurrencyPair(string $currencyPair, float $exchangeRate): self
    {
        $this->exchangeRates[$currencyPair] = $exchangeRate;
        return $this;
    }

    /**
     * @param Money $money
     * @param string $currency
     * @param int $precision
     * @return Money
     * @throws Exception\UnsupportedStrategyException
     * @throws ExchangeRateWasNotFoundException
     */
    public function exchange(Money $money, string $currency, int $precision = 2): Money
    {
        $currencyPair = "{$money->getCurrency()}-$currency";
        $reversePair = "$currency-{$money->getCurrency()}";
        switch (true) {
            case isset($this->exchangeRates[$currencyPair]):
                $exchangedAmount = round($money->getAmount() * $this->exchangeRates[$currencyPair], $precision);
                break;
            case isset($this->exchangeRates[$reversePair]):
                $exchangedAmount = round($money->getAmount() / $this->exchangeRates[$reversePair], $precision);
                break;
            default:
                throw new ExchangeRateWasNotFoundException($currencyPair);
        }
        return new Money($exchangedAmount, $currency, $money->getDifferentCurrencyBehaviorStrategy());
    }
}
