<?php

use Chetkov\Money\Exchanger;
use Chetkov\Money\ExchangerInterface;
use Chetkov\Money\Strategy\CurrencyConversationStrategyInterface;
use Chetkov\Money\Strategy\CurrencyConversionStrategy;

$exchangeRates = [
    'USD-RUB' => 66.34,
    'EUR-RUB' => 72.42,
    'JPY-RUB' => 0.61,
];

$exchangerFactory = static function () use ($exchangeRates): ExchangerInterface {
    static $instance;
    if (null === $instance) {
        $instance = Exchanger::getInstance($exchangeRates);
    }
    return $instance;
};

$currencyConversationStrategyFactory = static function () use ($exchangerFactory): CurrencyConversationStrategyInterface {
    static $instance;
    if (null === $instance) {
        $exchanger = $exchangerFactory();
        $instance = new CurrencyConversionStrategy($exchanger);
    }
    return $instance;
};

return [
    'use_currency_conversation_strategy' => true,
    'currency_conversation_strategy_factory' => $currencyConversationStrategyFactory,
];