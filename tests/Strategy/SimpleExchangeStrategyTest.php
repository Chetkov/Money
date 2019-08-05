<?php

namespace Tests\Chetkov\Money\Strategy;

use Chetkov\Money\DTO\PackageConfig;
use Chetkov\Money\Exception\ExchangeRateWasNotFoundException;
use Chetkov\Money\Exception\RequiredParameterMissedException;
use Chetkov\Money\Money;
use Chetkov\Money\Strategy\SimpleExchangeStrategy;
use PHPUnit\Framework\TestCase;

/**
 * Class SimpleExchangeStrategyTest
 * @package Tests\Chetkov\Money
 */
class SimpleExchangeStrategyTest extends TestCase
{
    /**
     * @throws RequiredParameterMissedException
     */
    protected function setUp()
    {
        $config = require CHETKOV_MONEY_ROOT . '/config/example.config.php';
        PackageConfig::getInstance($config);
    }

    public function test__construct(): void
    {
        new SimpleExchangeStrategy([]);
        $this->assertTrue(true);
    }

    /**
     * @throws \ReflectionException
     */
    public function testGetInstance(): void
    {
        $reflectionClass = new \ReflectionClass(SimpleExchangeStrategy::getInstance());
        $instanceProperty = $reflectionClass->getProperty('instance');
        $instanceProperty->setAccessible(true);
        $instanceProperty->setValue(null);
        $instanceProperty->setAccessible(false);

        SimpleExchangeStrategy::getInstance(['EUR-TRY' => 6.24]);
        $this->assertTrue(true);
    }

    public function testAddCurrencyPair(): void
    {
        $exchanger = SimpleExchangeStrategy::getInstance();
        $exchanger->addCurrencyPair('USD-RUB', 66.34);
        $this->assertTrue(true);
    }

    /**
     * @dataProvider exchangeDataProvider
     * @param Money $money
     * @param string $currency
     * @param array $exchangeConfig
     * @param array $expectedResult
     * @param int $roundingPrecision
     * @throws ExchangeRateWasNotFoundException
     * @throws RequiredParameterMissedException
     */
    public function testExchange(
        Money $money,
        string $currency,
        array $exchangeConfig,
        array $expectedResult,
        int $roundingPrecision = 2
    ): void {
        $exchanger = SimpleExchangeStrategy::getInstance();
        [$currencyPair, $exchangeRate] = $exchangeConfig;
        $exchanger->addCurrencyPair($currencyPair, $exchangeRate);

        $exchangedMoney = $exchanger->exchange($money, $currency, $roundingPrecision);
        $this->assertEquals($expectedResult, [$exchangedMoney->getAmount(), $exchangedMoney->getCurrency()]);
    }

    /**
     * @return array
     * @throws RequiredParameterMissedException
     */
    public function exchangeDataProvider(): array
    {
        $this->setUp();
        return [
            'USD to RUB' => [new Money(100, 'USD'), 'RUB', ['USD-RUB', 66.34], [6634, 'RUB']],
            'RUB to USD, precision: 2' => [new Money(100, 'RUB'), 'USD', ['USD-RUB', 66.34], [1.51, 'USD'], 2],
            'RUB to USD, precision: 3' => [new Money(100, 'RUB'), 'USD', ['USD-RUB', 66.34], [1.507, 'USD'], 3],
        ];
    }

    /**
     * @throws ExchangeRateWasNotFoundException
     * @throws RequiredParameterMissedException
     */
    public function testExchangeNegative(): void
    {
        $money = new Money(100, 'USD');
        $exchanger = SimpleExchangeStrategy::getInstance();

        $this->expectException(ExchangeRateWasNotFoundException::class);
        $exchanger->exchange($money, 'LEI');
    }
}
