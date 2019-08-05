<?php

namespace Chetkov\Money\Strategy;

use Chetkov\Money\Exception\ExchangeRateWasNotFoundException;
use Chetkov\Money\Exception\RequiredParameterMissedException;
use Chetkov\Money\Money;

/**
 * Class SimpleExchangeStrategy
 * @package Chetkov\Money\Strategy
 */
class SimpleExchangeStrategy implements ExchangeStrategyInterface
{
    /** @var SimpleExchangeStrategy */
    private static $instance;

    /** @var float[] */
    private $exchangeRates;

    /**
     * Exchanger constructor.
     * @param float[] $exchangeRates Example: ['USD-RUB' => 66.34]
     */
    public function __construct(array $exchangeRates = [])
    {
        $this->exchangeRates = $exchangeRates;
    }

    /**
     * @param float[] $exchangeRates Example: ['USD-RUB' => 66.34]
     * @return SimpleExchangeStrategy
     */
    public static function getInstance(array $exchangeRates = []): self
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }

        foreach ($exchangeRates as $currencyPair => $exchangeRate) {
            self::$instance->addCurrencyPair($currencyPair, $exchangeRate);
        }

        return self::$instance;
    }

    /**
     * @param string $currencyPair
     * @param float $exchangeRate
     * @return SimpleExchangeStrategy
     */
    public function addCurrencyPair(string $currencyPair, float $exchangeRate): self
    {
        $this->exchangeRates[$currencyPair] = $exchangeRate;
        return $this;
    }

    /**
     * @param Money $money
     * @param string $currency
     * @param int $roundingPrecision
     * @return Money
     * @throws RequiredParameterMissedException
     * @throws ExchangeRateWasNotFoundException
     */
    public function exchange(Money $money, string $currency, int $roundingPrecision = 2): Money
    {
        $currencyPair = "{$money->getCurrency()}-$currency";
        $reversePair = "$currency-{$money->getCurrency()}";
        switch (true) {
            case isset($this->exchangeRates[$currencyPair]):
                $exchangedAmount = round($money->getAmount() * $this->exchangeRates[$currencyPair], $roundingPrecision);
                break;
            case isset($this->exchangeRates[$reversePair]):
                $exchangedAmount = round($money->getAmount() / $this->exchangeRates[$reversePair], $roundingPrecision);
                break;
            default:
                throw new ExchangeRateWasNotFoundException($currencyPair);
        }
        return new Money($exchangedAmount, $currency);
    }
}
