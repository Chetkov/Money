<?php

namespace Tests\Chetkov\Money\Exchange\RatesLoading;

use Chetkov\Money\Exchange\RatesLoading\ExchangeRatesLoaderCacheDecorator;
use Chetkov\Money\Exchange\RatesLoading\SimpleExchangeRatesLoader;
use PHPUnit\Framework\TestCase;

/**
 * Class ExchangeRatesLoaderCacheDecoratorTest
 * @package Tests\Chetkov\Money\Exchange\RatesLoading
 */
class ExchangeRatesLoaderCacheDecoratorTest extends TestCase
{

    public function testLoad(): void
    {
        $simpleRatesLoader = new SimpleExchangeRatesLoader(['USD-RUB' => 66.34]);
        $cachingLoaderDecorator = new ExchangeRatesLoaderCacheDecorator($simpleRatesLoader, 1);

        $rates = $cachingLoaderDecorator->load();
        $simpleRatesLoader->addCurrencyPair('EUR-RUB', 72.5);

        $ratesFromCache = $cachingLoaderDecorator->load();
        sleep(2);
        $actualRates = $cachingLoaderDecorator->load();

        $assertion = $rates === $ratesFromCache && $actualRates !== $ratesFromCache;
        $this->assertTrue($assertion);
    }
}
