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
        $simpleRatesLoader = new SimpleExchangeRatesProvider(['USD-RUB' => 66.34]);
        $cachingLoaderDecorator = new ExchangeRatesProviderCacheDecorator($simpleRatesLoader, 1);

        $rates = $cachingLoaderDecorator->getRates();
        $simpleRatesLoader->addCurrencyPair('EUR-RUB', 72.5);

        $ratesFromCache = $cachingLoaderDecorator->getRates();
        sleep(2);
        $actualRates = $cachingLoaderDecorator->getRates();

        $assertion = $rates === $ratesFromCache && $actualRates !== $ratesFromCache;
        $this->assertTrue($assertion);
    }
}
