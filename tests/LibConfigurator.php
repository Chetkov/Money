<?php

namespace Tests\Chetkov\Money;

use Chetkov\Money\Exception\RequiredParameterMissedException;
use Chetkov\Money\Exchanger\ExchangerInterface;
use Chetkov\Money\Exchanger\GraphRatesSearchingExchangerDecorator;
use Chetkov\Money\Exchanger\RatesProvider\SimpleExchangeRatesProvider;
use Chetkov\Money\Exchanger\SimpleExchanger;
use Chetkov\Money\LibConfig;

/**
 * Class LibConfigurator
 * @package Tests\Chetkov\Money
 */
class LibConfigurator
{
    /**
     * @param bool $isCurrencyConversationEnabled
     * @param array $exchangeRates
     * @param \Closure|null $exchangerFactory
     * @throws RequiredParameterMissedException
     */
    public static function configureForTests(
        bool $isCurrencyConversationEnabled = null,
        array $exchangeRates = null,
        ?\Closure $exchangerFactory = null
    ): void {
        $isCurrencyConversationEnabled =
            $isCurrencyConversationEnabled ?? true;

        $exchangeRates =
            $exchangeRates ?? [
                'USD-RUB' => [66.34, 68.21],
                'EUR-RUB' => [72.42, 74.61],
                'JPY-RUB' => [0.61, 0.68],
            ];

        $exchangerFactory =
            $exchangerFactory ?? static function () use ($exchangeRates): ExchangerInterface {
                static $instance;
                if (null === $instance) {
                    $ratesProvider = SimpleExchangeRatesProvider::getInstance($exchangeRates);
                    $simpleExchanger = new SimpleExchanger($ratesProvider);
                    $instance = new GraphRatesSearchingExchangerDecorator($simpleExchanger, $ratesProvider);
                }
                return $instance;
            };

        $config = [
            'is_currency_conversation_enabled' => $isCurrencyConversationEnabled,
            'exchanger_factory' => $exchangerFactory,
        ];

        LibConfig::getInstance($config)->reconfigure($config);
    }
}
