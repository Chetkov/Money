<?php

use Chetkov\Money\Strategy\ExchangeStrategyInterface;
use Chetkov\Money\Strategy\SimpleExchangeStrategy;

$exchangeRates = [
    'USD-RUB' => 66.34,
    'EUR-RUB' => 72.42,
    'JPY-RUB' => 0.61,
];

return [
    'use_exchange_strategy' => true,
    'exchange_strategy_factory' => static function () use ($exchangeRates): ExchangeStrategyInterface {
        static $instance;
        if (null === $instance) {
            $instance = SimpleExchangeStrategy::getInstance($exchangeRates);
        }
        return $instance;
    },
];