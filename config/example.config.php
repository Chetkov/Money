<?php

use Chetkov\Money\Exchange\SimpleExchanger;
use Chetkov\Money\Exchange\ExchangerInterface;
use Chetkov\Money\Exchange\RatesLoading\SimpleExchangeRatesLoader;

$exchangeRates = [
    'USD-RUB' => 66.34,
    'EUR-RUB' => 72.42,
    'JPY-RUB' => 0.61,
];

return [
    'use_currency_conversation' => true,
    'exchanger_factory' => static function () use ($exchangeRates): ExchangerInterface {
        static $instance;
        if (null === $instance) {
            $ratesLoader = SimpleExchangeRatesLoader::getInstance($exchangeRates);
            $instance = new SimpleExchanger($ratesLoader);
        }
        return $instance;
    },
];