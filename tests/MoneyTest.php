<?php

namespace Tests\Chetkov\Money;

use Chetkov\Money\CurrencyEnum;
use Chetkov\Money\Exception\ExchangeRateWasNotFoundException;
use Chetkov\Money\Exception\OperationWithDifferentCurrenciesException;
use Chetkov\Money\Exception\RequiredParameterMissedException;
use Chetkov\Money\Money;
use PHPUnit\Framework\TestCase;

/**
 * Class MoneyTest
 * @package Tests\Chetkov\Money
 */
class MoneyTest extends TestCase
{
    private const RUB = CurrencyEnum::RUB;
    private const USD = CurrencyEnum::USD;
    private const EUR = CurrencyEnum::EUR;

    public function testGetAmount(): void
    {
        $money = Money::RUB(100);
        $this->assertEquals(100, $money->getAmount());
    }

    public function testGetCurrency(): void
    {
        $money = Money::RUB(100);
        $this->assertEquals(self::RUB, $money->getCurrency());
    }

    /**
     * @dataProvider exchangeDataProvider
     * @param Money $money
     * @param string $currency
     * @param array $expectedResult
     * @throws ExchangeRateWasNotFoundException
     * @throws OperationWithDifferentCurrenciesException
     */
    public function testExchange(Money $money, string $currency, array $expectedResult): void
    {
        $exchangedMoney = $money->exchange($currency);
        $this->assertEquals($expectedResult, [$exchangedMoney->getAmount(), $exchangedMoney->getCurrency()]);
    }

    /**
     * @return array
     */
    public function exchangeDataProvider(): array
    {
        return [
            'USD->RUB' => [Money::USD(100), self::RUB, [6634, self::RUB]],
            'USD<-RUB' => [Money::RUB(6821), self::USD, [100, self::USD]],
            'USD->RUB->EUR' => [Money::USD(100), self::EUR, [88.92, self::EUR]],
        ];
    }

    /**
     * @dataProvider addDataProvider
     * @param Money $one
     * @param Money $two
     * @param array $expectedResult
     * @throws OperationWithDifferentCurrenciesException
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
     */
    public function addDataProvider(): array
    {
        return [
            'float: RUB' => [Money::RUB(15.72), Money::RUB(14.29), [30.01, self::RUB]],
            'float: USD, RUB' => [Money::USD(100), Money::RUB(100), [101.47, self::USD]],
            'int: RUB, USD' => [Money::RUB(100), Money::USD(100), [6734, self::RUB]],
        ];
    }

    /**
     * @dataProvider subtractDataProvider
     * @param Money $one
     * @param Money $two
     * @param array $expectedResult
     * @throws ExchangeRateWasNotFoundException
     * @throws OperationWithDifferentCurrenciesException
     * @throws RequiredParameterMissedException
     */
    public function testSubtract(Money $one, Money $two, array $expectedResult): void
    {
        $three = $one->subtract($two);
        $this->assertEquals($expectedResult, [$three->getAmount(), $three->getCurrency()]);
    }

    /**
     * @return array
     */
    public function subtractDataProvider(): array
    {
        return [
            'int' => [Money::RUB(100), Money::RUB(100), [0, self::RUB]],
            'float' => [Money::RUB(15.72), Money::RUB(15.80), [-0.08, self::RUB]],
            'int: USD, RUB' => [Money::USD(100), Money::RUB(100), [98.53, self::USD]],
            'int: RUB, USD' => [Money::RUB(100), Money::USD(100), [-6534, self::RUB]],
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
     */
    public function multiplyDataProvider(): array
    {
        return [
            'int' => [Money::RUB(100), 100, [10000, self::RUB]],
            'float' => [Money::RUB(25), 1.5, [37.5, self::RUB]],
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
     */
    public function allocateEvenlyDataProvider(): array
    {
        return [
            'int' => [Money::RUB(100), 4, [[25, self::RUB], [25, self::RUB], [25, self::RUB], [25, self::RUB]]],
            'float' => [Money::RUB(5), 2, [[2.5, self::RUB], [2.5, self::RUB]]],
            'float, not in half' => [Money::RUB(100), 3, [[33.33, self::RUB], [33.33, self::RUB], [33.34, self::RUB]]],
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
     */
    public function allocateProportionallyDataProvider(): array
    {
        return [
            'int' => [Money::RUB(100), [0.4, 0.6], [[40, self::RUB], [60, self::RUB]]],
            'float' => [Money::RUB(5), [0.5, 0.5], [[2.5, self::RUB], [2.5, self::RUB]]],
            'float 1' => [Money::RUB(101), [0.5, 0.3, 0.2, 0.5], [[50.5, self::RUB], [30.3, self::RUB], [20.2, self::RUB], [50.5, self::RUB]]],
            'float 2' => [Money::RUB(33), [0.381, 0.476, 0.143], [[12.57, self::RUB], [15.71, self::RUB], [4.72, self::RUB]]],
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
     * @throws OperationWithDifferentCurrenciesException
     */
    public function testEquals(Money $one, Money $two, bool $isCrossCurrencyComparison, float $allowableDeviationPercent, bool $expectedResult): void
    {
        $result = $one->equals($two, $isCrossCurrencyComparison, $allowableDeviationPercent);
        $this->assertEquals($expectedResult, $result);
    }

    /**
     * @return array
     */
    public function equalsDataProvider(): array
    {
        return [
            'equals' => [Money::RUB(100), Money::RUB(100), false, 0, true],
            'not equals' => [Money::RUB(100), Money::RUB(200), false, 0, false],
            'equals (cross currency)' => [Money::RUB(100), Money::USD(1.51), true, 0.5, true],
            'not equals (cross currency)' => [Money::RUB(100), Money::USD(1), true, 0, false],
        ];
    }

    /**
     * @dataProvider moreThanDataProvider
     * @param Money $one
     * @param Money $two
     * @param bool $oneIsMoreThanTwo
     * @throws ExchangeRateWasNotFoundException
     * @throws OperationWithDifferentCurrenciesException
     */
    public function testMoreThan(Money $one, Money $two, bool $oneIsMoreThanTwo): void
    {
        $this->assertEquals($oneIsMoreThanTwo, $one->moreThan($two));
    }

    /**
     * @return array
     */
    public function moreThanDataProvider(): array
    {
        return [
            'true' => [Money::RUB(10), Money::RUB(5), true],
            'false' => [Money::RUB(5), Money::RUB(10), false],
        ];
    }

    /**
     * @dataProvider lessThanDataProvider
     * @param Money $one
     * @param Money $two
     * @param bool $oneIsLessThanTwo
     * @throws ExchangeRateWasNotFoundException
     * @throws OperationWithDifferentCurrenciesException
     */
    public function testLessThan(Money $one, Money $two, bool $oneIsLessThanTwo): void
    {
        $this->assertEquals($oneIsLessThanTwo, $one->lessThan($two));
    }

    /**
     * @return array
     */
    public function lessThanDataProvider(): array
    {
        return [
            'true' => [Money::RUB(10), Money::RUB(15), true],
            'false' => [Money::RUB(15), Money::RUB(10), false],
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
        LibConfigurator::configureForTests(false);
        $one = Money::RUB(100);
        $two = Money::USD(100);
        LibConfigurator::configureForTests();
        return [
            'different currencies for method add' => [
                static function () use ($one, $two) {
                    $one->add($two);
                },
                OperationWithDifferentCurrenciesException::class,
            ],
            'different currencies for method subtract' => [
                static function () use ($one, $two) {
                    $one->subtract($two);
                },
                OperationWithDifferentCurrenciesException::class,
            ],
            'different currencies for method moreThan' => [
                static function () use ($one, $two) {
                    $one->moreThan($two);
                },
                OperationWithDifferentCurrenciesException::class,
            ],
            'different currencies for method lessThan' => [
                static function () use ($one, $two) {
                    $one->lessThan($two);
                },
                OperationWithDifferentCurrenciesException::class,
            ],
        ];
    }

    public function test__toString(): void
    {
        $money = Money::RUB(100);
        $this->assertEquals(json_encode([
            'amount' => 100,
            'currency' => self::RUB,
        ]), (string)$money);
    }

    public function testJsonSerialize(): void
    {
        $money = Money::RUB(100);
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
