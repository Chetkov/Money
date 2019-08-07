<?php

namespace Tests\Chetkov\Money\Exchanger\RatesProvider;

use Chetkov\Money\Exception\RequiredParameterMissedException;
use Chetkov\Money\Exchanger\RatesProvider\SimpleExchangeRatesProvider;
use Chetkov\Money\LibConfig;
use PHPUnit\Framework\TestCase;

/**
 * Class SimpleExchangeRatesProviderTest
 * @package Tests\Chetkov\Money\Exchanger\RatesProvider
 */
class SimpleExchangeRatesProviderTest extends TestCase
{
    /**
     * @throws RequiredParameterMissedException
     */
    protected function setUp()
    {
        $config = require CHETKOV_MONEY_ROOT . '/config/example.config.php';
        LibConfig::getInstance($config);
    }

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
