<?php

use Chetkov\Money\Exchanger\ExchangerInterface;
use Chetkov\Money\Exchanger\RatesProvider\SimpleExchangeRatesProvider;
use Chetkov\Money\Exchanger\SimpleExchanger;

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
            $ratesLoader = SimpleExchangeRatesProvider::getInstance($exchangeRates);
            $instance = new SimpleExchanger($ratesLoader);
        }
        return $instance;
    },
];