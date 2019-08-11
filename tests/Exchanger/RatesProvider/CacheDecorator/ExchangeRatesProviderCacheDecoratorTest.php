<?php

namespace Tests\Chetkov\Money\Exchanger\RatesProvider\CacheDecorator;

use Chetkov\Money\Exchanger\RatesProvider\CacheDecorator\ExchangeRatesProviderCacheDecorator;
use Chetkov\Money\Exchanger\RatesProvider\CacheDecorator\Strategy\ClassPropertyCacheStrategy;
use Chetkov\Money\Exchanger\RatesProvider\SimpleExchangeRatesProvider;
use PHPUnit\Framework\TestCase;
use Psr\SimpleCache\InvalidArgumentException;

/**
 * Class ExchangeRatesProviderCacheDecoratorTest
 * @package Tests\Chetkov\Money\Exchanger\RatesProvider
 */
class ExchangeRatesProviderCacheDecoratorTest extends TestCase
{
    /**
     * @throws \Exception
     * @throws InvalidArgumentException
     */
    public function testGetRates(): void
    {
        $simpleRatesProvider = new SimpleExchangeRatesProvider(['USD-RUB' => 66.34]);
        $cacheStrategy = new ClassPropertyCacheStrategy();
        $cachingProviderDecorator = new ExchangeRatesProviderCacheDecorator($simpleRatesProvider, $cacheStrategy);

        $yesterdayDateTime = new \DateTimeImmutable('-1 day');

        $yesterdayRates = $cachingProviderDecorator->getRates($yesterdayDateTime);
        $simpleRatesProvider->addCurrencyPair('EUR-RUB', [72.5]);

        $todayRates = $cachingProviderDecorator->getRates();
        $simpleRatesProvider->addCurrencyPair('EUR-USD', [1.2]);

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
