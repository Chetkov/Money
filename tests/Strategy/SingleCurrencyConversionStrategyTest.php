<?php

namespace Tests\Chetkov\Money\Strategy;

use Chetkov\Money\Exception\ExchangeRateWasNotFoundException;
use Chetkov\Money\Exception\UnsupportedStrategyException;
use Chetkov\Money\Exchanger;
use Chetkov\Money\Money;
use Chetkov\Money\Strategy\SingleCurrencyConversionStrategy;
use PHPUnit\Framework\TestCase;

/**
 * Class SingleCurrencyConversionStrategyTest
 * @package Tests\Chetkov\Money\Strategy
 */
class SingleCurrencyConversionStrategyTest extends TestCase
{
    public function test__construct(): void
    {
        new SingleCurrencyConversionStrategy(new Exchanger([]));
        $this->assertTrue(true);
    }

    /**
     * @throws ExchangeRateWasNotFoundException
     * @throws UnsupportedStrategyException
     */
    public function testExecuteWithDifferentCurrencies(): void
    {
        $exchanger = new Exchanger([]);
        $exchanger->addCurrencyPair('USD-RUB', 66.34);
        $strategy = new SingleCurrencyConversionStrategy($exchanger);

        $money = new Money(100, 'USD');
        $result = $strategy->execute($money, 'RUB');
        $this->assertNotSame($money, $result);
    }

    /**
     * @throws ExchangeRateWasNotFoundException
     * @throws UnsupportedStrategyException
     */
    public function testExecuteWithSameCurrency(): void
    {
        $exchanger = new Exchanger([]);
        $strategy = new SingleCurrencyConversionStrategy($exchanger);

        $money = new Money(100, 'USD');
        $result = $strategy->execute($money, 'USD');
        $this->assertSame($money, $result);
    }
}
