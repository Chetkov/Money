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

    /** @var array */
    private $exchangeRates;

    /**
     * SimpleExchangeRatesProvider constructor.
     * @param array $exchangeRates
     */
    public function __construct(array $exchangeRates = [])
    {
        $this->exchangeRates = $exchangeRates;
    }

    /**
     * @param array $exchangeRates Example:
     * @return SimpleExchangeRatesProvider
     *
     * @example ['USD-RUB' => [66.34]]
     * @example ['USD-RUB' => [66.34, 68.12]]
     */
    public static function getInstance(array $exchangeRates = []): self
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }

        foreach ($exchangeRates as $currencyPair => $rates) {
            self::$instance->addCurrencyPair($currencyPair, $rates);
        }

        return self::$instance;
    }

    /**
     * @param string $currencyPair
     * @param array $exchangeRates [$sellingRate, $purchaseRate]
     * @return SimpleExchangeRatesProvider
     */
    public function addCurrencyPair(string $currencyPair, array $exchangeRates): self
    {
        $this->exchangeRates[$currencyPair] = $exchangeRates;
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
