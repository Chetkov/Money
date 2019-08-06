<?php

namespace Chetkov\Money\Exchange;

use Chetkov\Money\CurrencyEnum;
use Chetkov\Money\Exception\ExchangeRateWasNotFoundException;
use Chetkov\Money\Exception\RequiredParameterMissedException;
use Chetkov\Money\Exchange\RatesLoading\ExchangeRatesLoaderInterface;
use Chetkov\Money\Money;

/**
 * Class Exchanger
 * @package Chetkov\Money\Exchange
 */
class Exchanger implements ExchangerInterface
{
    /** @var ExchangeRatesLoaderInterface */
    private $exchangeRatesLoader;

    /**
     * Exchanger constructor.
     * @param ExchangeRatesLoaderInterface $exchangeRatesLoader
     */
    public function __construct(ExchangeRatesLoaderInterface $exchangeRatesLoader)
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
     * TODO: Искать по графу пути обмена через другие валюты для пар не связанных на прямую
     */
    public function exchange(Money $money, string $currency, int $roundingPrecision = 2): Money
    {
        $currencyPair = CurrencyEnum::getCurrencyPairCode($money->getCurrency(), $currency);
        $reversePair = CurrencyEnum::getCurrencyPairCode($currency, $money->getCurrency());
        $exchangeRates = $this->exchangeRatesLoader->load();
        switch (true) {
            case isset($exchangeRates[$currencyPair]):
                $exchangedAmount = round($money->getAmount() * $exchangeRates[$currencyPair], $roundingPrecision);
                break;
            case isset($exchangeRates[$reversePair]):
                $exchangedAmount = round($money->getAmount() / $exchangeRates[$reversePair], $roundingPrecision);
                break;
            default:
                throw new ExchangeRateWasNotFoundException($currencyPair);
        }
        return new Money($exchangedAmount, $currency);
    }
}
