<?php

namespace Tests\Chetkov\Money\Exchanger;

use Chetkov\Money\Exception\ExchangeRateWasNotFoundException;
use Chetkov\Money\Exception\RequiredParameterMissedException;
use Chetkov\Money\Exchanger\RatesProvider\SimpleExchangeRatesProvider;
use Chetkov\Money\Exchanger\SimpleExchanger;
use Chetkov\Money\LibConfig;
use Chetkov\Money\Money;
use PHPUnit\Framework\TestCase;

/**
 * Class SimpleExchangerTest
 * @package Tests\Chetkov\Money\Exchanger
 */
class SimpleExchangerTest extends TestCase
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
        $exchangeRatesLoader = SimpleExchangeRatesProvider::getInstance();
        [$currencyPair, $exchangeRate] = $exchangeConfig;
        $exchangeRatesLoader->addCurrencyPair($currencyPair, $exchangeRate);

        $exchanger = new SimpleExchanger($exchangeRatesLoader);

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
        $exchangeRatesLoader = SimpleExchangeRatesProvider::getInstance();
        $exchanger = new SimpleExchanger($exchangeRatesLoader);

        $this->expectException(ExchangeRateWasNotFoundException::class);
        $exchanger->exchange($money, 'LEI');
    }
}
