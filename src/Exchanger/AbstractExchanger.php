<?php

namespace Chetkov\Money\Exchanger;

use Chetkov\Money\Exception\ExchangeRateWasNotFoundException;
use Chetkov\Money\Exception\RequiredParameterMissedException;
use Chetkov\Money\Exchanger\RatesProvider\ExchangeRatesProviderInterface;
use Chetkov\Money\Helper\CurrencyPairHelper;
use Chetkov\Money\Money;

/**
 * Class AbstractExchanger
 * @package Chetkov\Money\Exchanger
 */
abstract class AbstractExchanger implements ExchangerInterface
{
    /** @var ExchangeRatesProviderInterface */
    protected $exchangeRatesProvider;

    /**
     * AbstractExchanger constructor.
     * @param ExchangeRatesProviderInterface $exchangeRatesProvider
     */
    public function __construct(ExchangeRatesProviderInterface $exchangeRatesProvider)
    {
        $this->exchangeRatesProvider = $exchangeRatesProvider;
    }

    /**
     * @param Money $money
     * @param string $currency
     * @param int $roundingPrecision
     * @return Money
     * @throws ExchangeRateWasNotFoundException
     * @throws RequiredParameterMissedException
     */
    public function exchange(Money $money, string $currency, int $roundingPrecision = 2): Money
    {
        $exchangeRates = $this->exchangeRatesProvider->getRates();

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
    abstract protected function doExchange(Money $money, string $currency, array $exchangeRates): float;

    /**
     * @param float $amount
     * @param string $sellingCurrency
     * @param string $purchasedCurrency
     * @param array $exchangeRates
     * @return float
     * @throws ExchangeRateWasNotFoundException
     */
    protected function calculateExchangeAmount(
        float $amount,
        string $sellingCurrency,
        string $purchasedCurrency,
        array $exchangeRates
    ): float {
        $currencyPair = CurrencyPairHelper::implode($sellingCurrency, $purchasedCurrency);
        $reversePair = CurrencyPairHelper::reverse($currencyPair);
        switch (true) {
            case isset($exchangeRates[$currencyPair]):
                $amount *= reset($exchangeRates[$currencyPair]);
                break;
            case isset($exchangeRates[$reversePair]):
                $amount /= end($exchangeRates[$reversePair]);
                break;
            default:
                throw new ExchangeRateWasNotFoundException($currencyPair);
        }
        return $amount;
    }
}
