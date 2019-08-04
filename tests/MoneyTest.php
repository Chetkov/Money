<?php

namespace Tests\Chetkov\Money;

use Chetkov\Money\Exception\OperationWithDifferentCurrenciesException;
use Chetkov\Money\Exception\UnsupportedStrategyException;
use Chetkov\Money\Money;
use Chetkov\Money\Strategy\ErrorWhenCurrenciesAreDifferentStrategy;
use Chetkov\Money\Strategy\SingleCurrencyConversionStrategy;
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
     * @throws UnsupportedStrategyException
     */
    public function test__construct(): void
    {
        new Money(100, self::RUB);
        $this->assertTrue(true);
    }

    /**
     * @throws UnsupportedStrategyException
     */
    public function testGetAmount(): void
    {
        $money = new Money(100, self::RUB);
        $this->assertEquals(100, $money->getAmount());
    }

    /**
     * @throws UnsupportedStrategyException
     */
    public function testGetCurrency(): void
    {
        $money = new Money(100, self::RUB);
        $this->assertEquals(self::RUB, $money->getCurrency());
    }

    /**
     * @dataProvider getDifferentCurrenciesBehaviorStrategyDataProvider
     * @param string $strategy
     * @throws UnsupportedStrategyException
     */
    public function testGetDifferentCurrenciesBehaviorStrategy(string $strategy): void
    {
        $money = new Money(100, self::RUB, $strategy);
        $this->assertEquals($money->getDifferentCurrencyBehaviorStrategy(), $strategy);
    }

    /**
     * @return array
     */
    public function getDifferentCurrenciesBehaviorStrategyDataProvider(): array
    {
        return [
            'ErrorWhenCurrenciesAreDifferentStrategy' => [ErrorWhenCurrenciesAreDifferentStrategy::class],
            'SingleCurrencyConversionStrategy' => [SingleCurrencyConversionStrategy::class],
        ];
    }

    /**
     * @dataProvider addDataProvider
     * @param Money $one
     * @param Money $two
     * @param array $expectedResult
     * @throws UnsupportedStrategyException
     */
    public function testAdd(Money $one, Money $two, array $expectedResult): void
    {
        $three = $one->add($two);
        $this->assertEquals($expectedResult, [$three->getAmount(), $three->getCurrency()]);
    }

    /**
     * @return array
     * @throws UnsupportedStrategyException
     */
    public function addDataProvider(): array
    {
        return [
            'int' => [new Money(100, self::RUB), new Money(100, self::RUB), [200, self::RUB]],
            'float' => [new Money(15.72, self::RUB), new Money(14.29, self::RUB), [30.01, self::RUB]],
        ];
    }

    /**
     * @dataProvider subtractDataProvider
     * @param Money $one
     * @param Money $two
     * @param array $expectedResult
     * @throws UnsupportedStrategyException
     */
    public function testSubtract(Money $one, Money $two, array $expectedResult): void
    {
        $three = $one->subtract($two);
        $this->assertEquals($expectedResult, [$three->getAmount(), $three->getCurrency()]);
    }

    /**
     * @return array
     * @throws UnsupportedStrategyException
     */
    public function subtractDataProvider(): array
    {
        return [
            'int' => [new Money(100, self::RUB), new Money(100, self::RUB), [0, self::RUB]],
            'float' => [new Money(15.72, self::RUB), new Money(15.80, self::RUB), [-0.08, self::RUB]],
        ];
    }

    /**
     * @dataProvider multiplyDataProvider
     * @param Money $money
     * @param float $factor
     * @param array $expectedResult
     * @throws UnsupportedStrategyException
     */
    public function testMultiple(Money $money, float $factor, array $expectedResult): void
    {
        $result = $money->multiple($factor);
        $this->assertEquals($expectedResult, [$result->getAmount(), $result->getCurrency()]);
    }

    /**
     * @return array
     * @throws UnsupportedStrategyException
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
     * @throws UnsupportedStrategyException
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
     * @throws UnsupportedStrategyException
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
     * @throws UnsupportedStrategyException
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
     * @throws UnsupportedStrategyException
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
     * @param bool $expectedResult
     */
    public function testEquals(Money $one, Money $two, bool $expectedResult): void
    {
        $result = $one->equals($two);
        $this->assertEquals($expectedResult, $result);
    }

    /**
     * @return array
     * @throws UnsupportedStrategyException
     */
    public function equalsDataProvider(): array
    {
        return [
            'equals' => [new Money(100, 'RUB'), new Money(100, 'RUB'), true],
            'not equals' => [new Money(100, 'RUB'), new Money(200, 'RUB'), false],
        ];
    }

    /**
     * @dataProvider moreThanDataProvider
     * @param Money $one
     * @param Money $two
     * @param bool $oneIsMoreThanTwo
     */
    public function testMoreThan(Money $one, Money $two, bool $oneIsMoreThanTwo): void
    {
        $this->assertEquals($oneIsMoreThanTwo, $one->moreThan($two));
    }

    /**
     * @return array
     * @throws UnsupportedStrategyException
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
     */
    public function testLessThan(Money $one, Money $two, bool $oneIsLessThanTwo): void
    {
        $this->assertEquals($oneIsLessThanTwo, $one->lessThan($two));
    }

    /**
     * @return array
     * @throws UnsupportedStrategyException
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
     * @throws UnsupportedStrategyException
     */
    public function negativeCasesDataProvider(): array
    {
        $one = new Money(100, self::RUB);
        $two = new Money(100, self::USD);
        return [
            'different currencies for method add' => [
                static function () use ($one, $two) {
                    return $one->add($two);
                },
                OperationWithDifferentCurrenciesException::class,
            ],
            'different currencies for method subtract' => [
                static function () use ($one, $two) {
                    return $one->subtract($two);
                },
                OperationWithDifferentCurrenciesException::class,
            ],
            'different currencies for method moreThan' => [
                static function () use ($one, $two) {
                    return $one->moreThan($two);
                },
                OperationWithDifferentCurrenciesException::class,
            ],
            'different currencies for method lessThan' => [
                static function () use ($one, $two) {
                    return $one->lessThan($two);
                },
                OperationWithDifferentCurrenciesException::class,
            ],
        ];
    }

    /**
     * @throws UnsupportedStrategyException
     */
    public function test__toString(): void
    {
        $money = new Money(100, self::RUB);
        $this->assertEquals(json_encode([
            'amount' => 100,
            'currency' => self::RUB,
            'different_currency_behavior_strategy' => ErrorWhenCurrenciesAreDifferentStrategy::class,
        ]), (string)$money);
    }

    /**
     * @throws UnsupportedStrategyException
     */
    public function testJsonSerialize(): void
    {
        $money = new Money(100, self::RUB);
        $this->assertSame('"' . json_encode([
                'amount' => 100,
                'currency' => self::RUB,
                'different_currency_behavior_strategy' => ErrorWhenCurrenciesAreDifferentStrategy::class,
            ]) . '"', stripslashes(json_encode($money)));
    }

    /**
     * @throws UnsupportedStrategyException
     */
    public function testFromJSON(): void
    {
        Money::fromJSON('{"amount":100, "currency":"RUB"}');
        $this->assertTrue(true);
    }

    /**
     * @throws UnsupportedStrategyException
     */
    public function testFromJSONWithBehaviorStrategy(): void
    {
        Money::fromJSON('{"amount":100, "currency":"RUB", "different_currency_behavior_strategy":"Chetkov\\\Money\\\Strategy\\\SingleCurrencyConversionStrategy"}');
        $this->assertTrue(true);
    }
}
