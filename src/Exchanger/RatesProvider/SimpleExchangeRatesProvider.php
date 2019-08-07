<?php

namespace Chetkov\Money\Exchanger\RatesProvider;

/**
 * Class SimpleExchangeRatesProvider
 * @package Chetkov\Money\Exchanger\RatesProvider
 */
class SimpleExchangeRatesProvider implements ExchangeRatesProviderInterface
{
    /** @var SimpleExchangeRatesProvider */
    private static $instance;

    /** @var float[] */
    private $exchangeRates;

    /**
     * SimpleExchangeRatesProvider constructor.
     * @param float[] $exchangeRates Example: ['USD-RUB' => 66.34]
     */
    public function __construct(array $exchangeRates = [])
    {
        $this->exchangeRates = $exchangeRates;
    }

    /**
     * @param float[] $exchangeRates Example: ['USD-RUB' => 66.34]
     * @return SimpleExchangeRatesProvider
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
     * @return SimpleExchangeRatesProvider
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
    public function getRates(?\DateTimeImmutable $dateTime = null): array
    {
        return $this->exchangeRates;
    }
}
