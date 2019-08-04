<?php

namespace Tests\Chetkov\Money;

use Chetkov\Money\Exception\ExchangeRateWasNotFoundException;
use Chetkov\Money\Exception\UnsupportedStrategyException;
use Chetkov\Money\Exchanger;
use Chetkov\Money\Money;
use PHPUnit\Framework\TestCase;

/**
 * Class ExchangerTest
 * @package Tests\Chetkov\Money
 */
class ExchangerTest extends TestCase
{
    public function test__construct(): void
    {
        new Exchanger([]);
        $this->assertTrue(true);
    }

    public function testGetInstance(): void
    {
        Exchanger::getInstance();
        $this->assertTrue(true);
    }

    public function testAddCurrencyPair(): void
    {
        $exchanger = Exchanger::getInstance();
        $exchanger->addCurrencyPair('USD-RUB', 66.34);
        $this->assertTrue(true);
    }

    /**
     * @dataProvider exchangeDataProvider
     * @param Money $money
     * @param string $currency
     * @param array $exchangeConfig
     * @param array $expectedResult
     * @param int $precision
     * @throws ExchangeRateWasNotFoundException
     * @throws UnsupportedStrategyException
     */
    public function testExchange(
        Money $money,
        string $currency,
        array $exchangeConfig,
        array $expectedResult,
        int $precision = 2
    ): void {
        $exchanger = Exchanger::getInstance();
        [$currencyPair, $exchangeRate] = $exchangeConfig;
        $exchanger->addCurrencyPair($currencyPair, $exchangeRate);

        $exchangedMoney = $exchanger->exchange($money, $currency, $precision);
        $this->assertEquals($expectedResult, [$exchangedMoney->getAmount(), $exchangedMoney->getCurrency()]);
    }

    /**
     * @return array
     * @throws UnsupportedStrategyException
     */
    public function exchangeDataProvider(): array
    {
        return [
            'USD to RUB' => [new Money(100, 'USD'), 'RUB', ['USD-RUB', 66.34], [6634, 'RUB']],
            'RUB to USD, precision: 2' => [new Money(100, 'RUB'), 'USD', ['USD-RUB', 66.34], [1.51, 'USD'], 2],
            'RUB to USD, precision: 3' => [new Money(100, 'RUB'), 'USD', ['USD-RUB', 66.34], [1.507, 'USD'], 3],
        ];
    }

    /**
     * @throws ExchangeRateWasNotFoundException
     * @throws UnsupportedStrategyException
     */
    public function testExchangeNegative(): void
    {
        $money = new Money(100, 'USD');
        $exchanger = Exchanger::getInstance();

        $this->expectException(ExchangeRateWasNotFoundException::class);
        $exchanger->exchange($money, 'LEI');
    }
}
