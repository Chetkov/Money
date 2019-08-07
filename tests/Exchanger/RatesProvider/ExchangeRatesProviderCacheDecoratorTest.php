<?php

namespace Tests\Chetkov\Money\Exchanger\RatesProvider;

use Chetkov\Money\Exchanger\RatesProvider\ExchangeRatesProviderCacheDecorator;
use Chetkov\Money\Exchanger\RatesProvider\SimpleExchangeRatesProvider;
use PHPUnit\Framework\TestCase;

/**
 * Class ExchangeRatesProviderCacheDecoratorTest
 * @package Tests\Chetkov\Money\Exchanger\RatesProvider
 */
class ExchangeRatesProviderCacheDecoratorTest extends TestCase
{

    public function testLoad(): void
    {
        $simpleRatesProvider = new SimpleExchangeRatesProvider(['USD-RUB' => 66.34]);
        $cachingProviderDecorator = new ExchangeRatesProviderCacheDecorator($simpleRatesProvider, 1);

        $rates = $cachingProviderDecorator->getRates();
        $simpleRatesProvider->addCurrencyPair('EUR-RUB', 72.5);

        $ratesFromCache = $cachingProviderDecorator->getRates();
        sleep(2);
        $actualRates = $cachingProviderDecorator->getRates();

        $assertion = $rates === $ratesFromCache && $actualRates !== $ratesFromCache;
        $this->assertTrue($assertion);
    }
}
