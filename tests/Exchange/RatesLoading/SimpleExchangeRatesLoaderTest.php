<?php

namespace Tests\Chetkov\Money\Exchange\RatesLoading;

use Chetkov\Money\Exception\RequiredParameterMissedException;
use Chetkov\Money\Exchange\RatesLoading\SimpleExchangeRatesLoader;
use Chetkov\Money\LibConfig;
use PHPUnit\Framework\TestCase;

/**
 * Class SimpleExchangeRatesLoaderTest
 * @package Tests\Chetkov\Money\Exchange\RatesLoading
 */
class SimpleExchangeRatesLoaderTest extends TestCase
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
        $reflectionClass = new \ReflectionClass(SimpleExchangeRatesLoader::getInstance());
        $instanceProperty = $reflectionClass->getProperty('instance');
        $instanceProperty->setAccessible(true);
        $instanceProperty->setValue(null);
        $instanceProperty->setAccessible(false);

        SimpleExchangeRatesLoader::getInstance(['EUR-TRY' => 6.24]);
        $this->assertTrue(true);
    }

    public function testAddCurrencyPair(): void
    {
        $exchanger = SimpleExchangeRatesLoader::getInstance();
        $exchanger->addCurrencyPair('USD-RUB', 66.34);
        $this->assertTrue(true);
    }
}
