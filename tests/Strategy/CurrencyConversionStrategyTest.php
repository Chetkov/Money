<?php

namespace Tests\Chetkov\Money\Strategy;

use Chetkov\Money\DTO\PackageConfig;
use Chetkov\Money\Exception\ExchangeRateWasNotFoundException;
use Chetkov\Money\Exception\RequiredParameterMissedException;
use Chetkov\Money\Exchanger;
use Chetkov\Money\Money;
use Chetkov\Money\Strategy\CurrencyConversionStrategy;
use PHPUnit\Framework\TestCase;

/**
 * Class CurrencyConversionStrategyTest
 * @package Tests\Chetkov\Money\Strategy
 */
class CurrencyConversionStrategyTest extends TestCase
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
        new CurrencyConversionStrategy(new Exchanger([]));
        $this->assertTrue(true);
    }

    /**
     * @throws ExchangeRateWasNotFoundException
     * @throws RequiredParameterMissedException
     */
    public function testExecuteWithDifferentCurrencies(): void
    {
        $exchanger = new Exchanger([]);
        $exchanger->addCurrencyPair('USD-RUB', 66.34);
        $strategy = new CurrencyConversionStrategy($exchanger);

        $one = new Money(100, 'USD');
        $two = new Money(100, 'RUB');

        $result = $strategy->convert($one, $two);
        $this->assertNotSame($one, $result);
    }

    /**
     * @throws ExchangeRateWasNotFoundException
     * @throws RequiredParameterMissedException
     */
    public function testExecuteWithSameCurrency(): void
    {
        $exchanger = new Exchanger([]);
        $strategy = new CurrencyConversionStrategy($exchanger);

        $one = new Money(100, 'USD');
        $two = new Money(100, 'USD');
        $result = $strategy->convert($one, $two);
        $this->assertSame($one, $result);
    }
}
