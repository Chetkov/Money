<?php

namespace Tests\Chetkov\Money\Exchanger\RatesProvider;

use Chetkov\Money\Exchanger\RatesProvider\SimpleExchangeRatesProvider;
use PHPUnit\Framework\TestCase;

/**
 * Class SimpleExchangeRatesProviderTest
 * @package Tests\Chetkov\Money\Exchanger\RatesProvider
 */
class SimpleExchangeRatesProviderTest extends TestCase
{
    /**
     * @throws \ReflectionException
     */
    public function testGetInstance(): void
    {
        $reflectionClass = new \ReflectionClass(SimpleExchangeRatesProvider::getInstance());
        $instanceProperty = $reflectionClass->getProperty('instance');
        $instanceProperty->setAccessible(true);
        $instanceProperty->setValue(null);
        $instanceProperty->setAccessible(false);

        SimpleExchangeRatesProvider::getInstance(['EUR-TRY' => [6.24]]);
        $this->assertTrue(true);
    }

    public function testAddCurrencyPair(): void
    {
        $exchanger = SimpleExchangeRatesProvider::getInstance();
        $exchanger->addCurrencyPair('USD-RUB', [66.34]);
        $this->assertTrue(true);
    }
}
