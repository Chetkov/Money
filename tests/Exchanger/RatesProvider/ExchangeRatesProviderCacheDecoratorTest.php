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
    /**
     * @throws \Exception
     */
    public function testLoad(): void
    {
        $simpleRatesProvider = new SimpleExchangeRatesProvider(['USD-RUB' => 66.34]);
        $cachingProviderDecorator = new ExchangeRatesProviderCacheDecorator($simpleRatesProvider, 1);

        $yesterdayDateTime = new \DateTimeImmutable('-1 day');

        $yesterdayRates = $cachingProviderDecorator->getRates($yesterdayDateTime);
        $simpleRatesProvider->addCurrencyPair('EUR-RUB', 72.5);

        $todayRates = $cachingProviderDecorator->getRates();
        $simpleRatesProvider->addCurrencyPair('EUR-USD', 1.2);

        $yesterdayRatesFromCache = $cachingProviderDecorator->getRates($yesterdayDateTime);
        $todayRatesFromCache = $cachingProviderDecorator->getRates();

        sleep(2);
        $actualYesterdayRates = $cachingProviderDecorator->getRates($yesterdayDateTime);
        $actualTodayRates = $cachingProviderDecorator->getRates();

        $assertionForYesterdayRates =
            $yesterdayRates === $yesterdayRatesFromCache
            && $actualYesterdayRates !== $yesterdayRatesFromCache;

        $assertionForTodayRates =
            $todayRates === $todayRatesFromCache
            && $actualTodayRates !== $todayRatesFromCache;

        $this->assertTrue($assertionForYesterdayRates && $assertionForTodayRates);
    }
}
