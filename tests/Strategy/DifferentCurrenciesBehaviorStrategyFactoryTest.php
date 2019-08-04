<?php

namespace Tests\Chetkov\Money\Strategy;

use Chetkov\Money\Exception\UnsupportedStrategyException;
use Chetkov\Money\Strategy\DifferentCurrenciesBehaviorStrategyFactory;
use Chetkov\Money\Strategy\ErrorWhenCurrenciesAreDifferentStrategy;
use Chetkov\Money\Strategy\SingleCurrencyConversionStrategy;
use PHPUnit\Framework\TestCase;

/**
 * Class DifferentCurrenciesBehaviorStrategyFactoryTest
 * @package Tests\Chetkov\Money\Strategy
 */
class DifferentCurrenciesBehaviorStrategyFactoryTest extends TestCase
{
    /**
     * @dataProvider createDataProvider
     * @param string $strategyClass
     * @throws UnsupportedStrategyException
     */
    public function testCreate(string $strategyClass): void
    {
        $strategy = DifferentCurrenciesBehaviorStrategyFactory::create($strategyClass);
        $this->assertEquals($strategyClass, get_class($strategy));
    }

    /**
     * @return array
     */
    public function createDataProvider(): array
    {
        return [
            'ErrorWhenCurrenciesAreDifferentStrategy' => [ErrorWhenCurrenciesAreDifferentStrategy::class],
            'SingleCurrencyConversionStrategy' => [SingleCurrencyConversionStrategy::class],
        ];
    }

    /**
     * @throws UnsupportedStrategyException
     */
    public function testCreateNegative(): void
    {
        $this->expectException(UnsupportedStrategyException::class);
        DifferentCurrenciesBehaviorStrategyFactory::create('SomeStrategy');
    }
}
