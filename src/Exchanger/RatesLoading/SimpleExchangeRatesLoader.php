<?php

namespace Chetkov\Money\Exchanger\RatesLoading;

/**
 * Class SimpleExchangeRatesLoader
 * @package Chetkov\Money\Exchanger\RatesLoading
 */
class SimpleExchangeRatesLoader implements ExchangeRatesLoaderInterface
{
    /** @var SimpleExchangeRatesLoader */
    private static $instance;

    /** @var float[] */
    private $exchangeRates;

    /**
     * SimpleExchangeRatesLoader constructor.
     * @param float[] $exchangeRates Example: ['USD-RUB' => 66.34]
     */
    public function __construct(array $exchangeRates = [])
    {
        $this->exchangeRates = $exchangeRates;
    }

    /**
     * @param float[] $exchangeRates Example: ['USD-RUB' => 66.34]
     * @return SimpleExchangeRatesLoader
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
     * @return SimpleExchangeRatesLoader
     */
    public function addCurrencyPair(string $currencyPair, float $exchangeRate): self
    {
        $this->exchangeRates[$currencyPair] = $exchangeRate;
        return $this;
    }

    /**
     * @param \DateTimeImmutable|null $dateTime
     * @return array
     */
    public function load(?\DateTimeImmutable $dateTime = null): array
    {
        return $this->exchangeRates;
    }
}
