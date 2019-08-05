<?php

namespace Tests\Chetkov\Money;

use Chetkov\Money\DTO\PackageConfig;
use Chetkov\Money\Exception\ExchangeRateWasNotFoundException;
use Chetkov\Money\Exception\ExchangeStrategyIsNotSetException;
use Chetkov\Money\Exception\RequiredParameterMissedException;
use Chetkov\Money\Money;
use PHPUnit\Framework\TestCase;

/**
 * Class MoneyTest
 * @package Tests\Chetkov\Money
 */
class MoneyTest extends TestCase
{
    private const RUB = 'RUB';
    private const USD = 'USD';

    /**
     * @throws RequiredParameterMissedException
     */
    protected function setUp()
    {
        $config = require CHETKOV_MONEY_ROOT . '/config/example.config.php';
        PackageConfig::getInstance($config);
    }

    /**
     * @throws RequiredParameterMissedException
     */
    public function test__construct(): void
    {
        new Money(100, self::RUB);
        $this->assertTrue(true);
    }

    /**
     * @throws RequiredParameterMissedException
     */
    public function testGetAmount(): void
    {
        $money = new Money(100, self::RUB);
        $this->assertEquals(100, $money->getAmount());
    }

    /**
     * @throws RequiredParameterMissedException
     */
    public function testGetCurrency(): void
    {
        $money = new Money(100, self::RUB);
        $this->assertEquals(self::RUB, $money->getCurrency());
    }

    /**
     * @dataProvider addDataProvider
     * @param Money $one
     * @param Money $two
     * @param array $expectedResult
     * @throws ExchangeStrategyIsNotSetException
     * @throws RequiredParameterMissedException
     * @throws ExchangeRateWasNotFoundException
     */
    public function testAdd(Money $one, Money $two, array $expectedResult): void
    {
        $three = $one->add($two);
        $this->assertEquals($expectedResult, [$three->getAmount(), $three->getCurrency()]);
    }

    /**
     * @return array
     * @throws RequiredParameterMissedException
     */
    public function addDataProvider(): array
    {
        $this->setUp();
        return [
            'float: RUB' => [new Money(15.72, self::RUB), new Money(14.29, self::RUB), [30.01, self::RUB]],
            'float: USD, RUB' => [new Money(100, self::USD), new Money(100, self::RUB), [101.51, self::USD]],
            'int: RUB, USD' => [new Money(100, self::RUB), new Money(100, self::USD), [6734, self::RUB]],
        ];
    }

    /**
     * @dataProvider subtractDataProvider
     * @param Money $one
     * @param Money $two
     * @param array $expectedResult
     * @throws ExchangeRateWasNotFoundException
     * @throws ExchangeStrategyIsNotSetException
     * @throws RequiredParameterMissedException
     */
    public function testSubtract(Money $one, Money $two, array $expectedResult): void
    {
        $three = $one->subtract($two);
        $this->assertEquals($expectedResult, [$three->getAmount(), $three->getCurrency()]);
    }

    /**
     * @return array
     * @throws RequiredParameterMissedException
     */
    public function subtractDataProvider(): array
    {
        $this->setUp();
        return [
            'int' => [new Money(100, self::RUB), new Money(100, self::RUB), [0, self::RUB]],
            'float' => [new Money(15.72, self::RUB), new Money(15.80, self::RUB), [-0.08, self::RUB]],
            'int: USD, RUB' => [new Money(100, self::USD), new Money(100, self::RUB), [98.49, self::USD]],
            'int: RUB, USD' => [new Money(100, self::RUB), new Money(100, self::USD), [-6534, self::RUB]],
        ];
    }

    /**
     * @dataProvider multiplyDataProvider
     * @param Money $money
     * @param float $factor
     * @param array $expectedResult
     * @throws RequiredParameterMissedException
     */
    public function testMultiple(Money $money, float $factor, array $expectedResult): void
    {
        $result = $money->multiple($factor);
        $this->assertEquals($expectedResult, [$result->getAmount(), $result->getCurrency()]);
    }

    /**
     * @return array
     * @throws RequiredParameterMissedException
     */
    public function multiplyDataProvider(): array
    {
        return [
            'int' => [new Money(100, self::RUB), 100, [10000, self::RUB]],
            'float' => [new Money(25, self::RUB), 1.5, [37.5, self::RUB]],
        ];
    }

    /**
     * @dataProvider allocateEvenlyDataProvider
     * @param Money $money
     * @param int $n
     * @param array $expectedResult
     * @throws RequiredParameterMissedException
     */
    public function testAllocateEvenly(Money $money, int $n, array $expectedResult): void
    {
        $allocatedList = $money->allocateEvenly($n);

        $result = [];
        foreach ($allocatedList as $item) {
            $result[] = [$item->getAmount(), $item->getCurrency()];
        }

        $this->assertEquals($expectedResult, $result);
    }

    /**
     * @return array
     * @throws RequiredParameterMissedException
     */
    public function allocateEvenlyDataProvider(): array
    {
        return [
            'int' => [new Money(100, self::RUB), 4, [[25, self::RUB], [25, self::RUB], [25, self::RUB], [25, self::RUB]]],
            'float' => [new Money(5, self::RUB), 2, [[2.5, self::RUB], [2.5, self::RUB]]],
            'float, not in half' => [new Money(100, self::RUB), 3, [[33.33, self::RUB], [33.33, self::RUB], [33.34, self::RUB]]],
        ];
    }

    /**
     * @dataProvider allocateProportionallyDataProvider
     * @param Money $money
     * @param array $ratios
     * @param array $expectedResult
     * @throws RequiredParameterMissedException
     */
    public function testAllocateProportionally(Money $money, array $ratios, array $expectedResult): void
    {
        $allocatedList = $money->allocateProportionally($ratios);

        $result = [];
        foreach ($allocatedList as $item) {
            $result[] = [$item->getAmount(), $item->getCurrency()];
        }

        $this->assertEquals($expectedResult, $result);
    }

    /**
     * @return array
     * @throws RequiredParameterMissedException
     */
    public function allocateProportionallyDataProvider(): array
    {
        return [
            'int' => [new Money(100, self::RUB), [0.4, 0.6], [[40, self::RUB], [60, self::RUB]]],
            'float' => [new Money(5, self::RUB), [0.5, 0.5], [[2.5, self::RUB], [2.5, self::RUB]]],
            'float 1' => [new Money(101, self::RUB), [0.5, 0.3, 0.2, 0.5], [[50.5, self::RUB], [30.3, self::RUB], [20.2, self::RUB], [50.5, self::RUB]]],
            'float 2' => [new Money(33, self::RUB), [0.381, 0.476, 0.143], [[12.57, self::RUB], [15.71, self::RUB], [4.72, self::RUB]]],
        ];
    }

    /**
     * @dataProvider equalsDataProvider
     * @param Money $one
     * @param Money $two
     * @param bool $isCrossCurrencyComparison
     * @param float $allowableDeviationPercent
     * @param bool $expectedResult
     * @throws ExchangeRateWasNotFoundException
     * @throws ExchangeStrategyIsNotSetException
     */
    public function testEquals(Money $one, Money $two, bool $isCrossCurrencyComparison, float $allowableDeviationPercent, bool $expectedResult): void
    {
        $result = $one->equals($two, $isCrossCurrencyComparison, $allowableDeviationPercent);
        $this->assertEquals($expectedResult, $result);
    }

    /**
     * @return array
     * @throws RequiredParameterMissedException
     */
    public function equalsDataProvider(): array
    {
        return [
            'equals' => [new Money(100, self::RUB), new Money(100, self::RUB), false, 0, true],
            'not equals' => [new Money(100, self::RUB), new Money(200, self::RUB), false, 0, false],
            'equals (cross currency)' => [new Money(100, self::RUB), new Money(1.51, self::USD), true, 0.5, true],
            'not equals (cross currency)' => [new Money(100, self::RUB), new Money(1, self::USD), true, 0, false],
        ];
    }

    /**
     * @dataProvider moreThanDataProvider
     * @param Money $one
     * @param Money $two
     * @param bool $oneIsMoreThanTwo
     * @throws ExchangeRateWasNotFoundException
     * @throws ExchangeStrategyIsNotSetException
     */
    public function testMoreThan(Money $one, Money $two, bool $oneIsMoreThanTwo): void
    {
        $this->assertEquals($oneIsMoreThanTwo, $one->moreThan($two));
    }

    /**
     * @return array
     * @throws RequiredParameterMissedException
     */
    public function moreThanDataProvider(): array
    {
        return [
            'true' => [new Money(10, self::RUB), new Money(5, self::RUB), true],
            'false' => [new Money(5, self::RUB), new Money(10, self::RUB), false],
        ];
    }

    /**
     * @dataProvider lessThanDataProvider
     * @param Money $one
     * @param Money $two
     * @param bool $oneIsLessThanTwo
     * @throws ExchangeRateWasNotFoundException
     * @throws ExchangeStrategyIsNotSetException
     */
    public function testLessThan(Money $one, Money $two, bool $oneIsLessThanTwo): void
    {
        $this->assertEquals($oneIsLessThanTwo, $one->lessThan($two));
    }

    /**
     * @return array
     * @throws RequiredParameterMissedException
     */
    public function lessThanDataProvider(): array
    {
        return [
            'true' => [new Money(10, self::RUB), new Money(15, self::RUB), true],
            'false' => [new Money(15, self::RUB), new Money(10, self::RUB), false],
        ];
    }

    /**
     * @dataProvider negativeCasesDataProvider
     * @param \Closure $closure
     * @param string $exceptionClass
     */
    public function testNegativeCases(\Closure $closure, string $exceptionClass): void
    {
        $this->expectException($exceptionClass);
        $closure();
    }

    /**
     * @return array
     * @throws RequiredParameterMissedException
     */
    public function negativeCasesDataProvider(): array
    {
        $config = require CHETKOV_MONEY_ROOT . '/config/example.config.php';
        $reconfigurePackageConfig = static function (bool $useStrategy) use ($config) {
            $config['use_exchange_strategy'] = $useStrategy;
            PackageConfig::getInstance()->reconfigure($config);
        };

        $reconfigurePackageConfig(false);
        $one = new Money(100, self::RUB);
        $two = new Money(100, self::USD);
        $reconfigurePackageConfig(true);
        return [
            'different currencies for method add' => [
                static function () use ($one, $two) {
                    $one->add($two);
                },
                ExchangeStrategyIsNotSetException::class,
            ],
            'different currencies for method subtract' => [
                static function () use ($one, $two) {
                    $one->subtract($two);
                },
                ExchangeStrategyIsNotSetException::class,
            ],
            'different currencies for method moreThan' => [
                static function () use ($one, $two) {
                    $one->moreThan($two);
                },
                ExchangeStrategyIsNotSetException::class,
            ],
            'different currencies for method lessThan' => [
                static function () use ($one, $two) {
                    $one->lessThan($two);
                },
                ExchangeStrategyIsNotSetException::class,
            ],
        ];
    }

    /**
     * @throws RequiredParameterMissedException
     */
    public function test__toString(): void
    {
        $money = new Money(100, self::RUB);
        $this->assertEquals(json_encode([
            'amount' => 100,
            'currency' => self::RUB,
        ]), (string)$money);
    }

    /**
     * @throws RequiredParameterMissedException
     */
    public function testJsonSerialize(): void
    {
        $money = new Money(100, self::RUB);
        $this->assertSame('"{\"amount\":100,\"currency\":\"RUB\"}"', json_encode($money));
    }

    /**
     * @throws RequiredParameterMissedException
     */
    public function testFromJSON(): void
    {
        Money::fromJSON('{"amount":100, "currency":"RUB"}');
        $this->assertTrue(true);
    }
}
