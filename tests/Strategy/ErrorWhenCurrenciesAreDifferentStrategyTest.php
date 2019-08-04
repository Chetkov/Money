<?php

namespace Tests\Chetkov\Money\Strategy;

use Chetkov\Money\Exception\OperationWithDifferentCurrenciesException;
use Chetkov\Money\Exception\UnsupportedStrategyException;
use Chetkov\Money\Money;
use Chetkov\Money\Strategy\ErrorWhenCurrenciesAreDifferentStrategy;
use PHPUnit\Framework\TestCase;

/**
 * Class ErrorWhenCurrenciesAreDifferentStrategyTest
 * @package Tests\Chetkov\Money\Strategy
 */
class ErrorWhenCurrenciesAreDifferentStrategyTest extends TestCase
{
    /**
     * @throws OperationWithDifferentCurrenciesException
     * @throws UnsupportedStrategyException
     */
    public function testExecute(): void
    {
        $strategy = new ErrorWhenCurrenciesAreDifferentStrategy();
        $money = new Money(100, 'USD');
        $strategy->execute($money, 'USD');
        $this->assertTrue(true);
    }

    /**
     * @throws OperationWithDifferentCurrenciesException
     * @throws UnsupportedStrategyException
     */
    public function testExecuteNegative(): void
    {
        $strategy = new ErrorWhenCurrenciesAreDifferentStrategy();
        $money = new Money(100, 'USD');

        $this->expectException(OperationWithDifferentCurrenciesException::class);
        $strategy->execute($money, 'RUB');
    }
}
