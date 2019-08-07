<?php

namespace Chetkov\Money\Exchanger;

use Chetkov\Money\Exception\ExchangeRateWasNotFoundException;
use Chetkov\Money\Exception\RequiredParameterMissedException;
use Chetkov\Money\Exchanger\RatesProvider\ExchangeRatesProviderInterface;
use Chetkov\Money\Money;

/**
 * Class AbstractExchanger
 * @package Chetkov\Money\Exchanger
 */
abstract class AbstractExchanger implements ExchangerInterface
{
    /** @var ExchangeRatesProviderInterface */
    protected $exchangeRatesLoader;

    /**
     * AbstractExchanger constructor.
     * @param ExchangeRatesProviderInterface $exchangeRatesLoader
     */
    public function __construct(ExchangeRatesProviderInterface $exchangeRatesLoader)
    {
        $this->exchangeRatesLoader = $exchangeRatesLoader;
    }

    /**
     * @param Money $money
     * @param string $currency
     * @param int $roundingPrecision
     * @return Money
     * @throws ExchangeRateWasNotFoundException
     * @throws RequiredParameterMissedException
     * TODO: Учитывать курс покупки/продажи
     */
    public function exchange(Money $money, string $currency, int $roundingPrecision = 2): Money
    {
        $exchangeRates = $this->exchangeRatesLoader->getRates();

        $exchangedAmount = $this->doExchange($money, $currency, $exchangeRates);
        $exchangedAmount = round($exchangedAmount, $roundingPrecision);

        return new Money($exchangedAmount, $currency);
    }

    /**
     * @param Money $money
     * @param string $currency
     * @param array $exchangeRates
     * @return float
     * @throws ExchangeRateWasNotFoundException
     */
    abstract protected function doExchange(
        Money $money,
        string $currency,
        array $exchangeRates
    ): float;
}