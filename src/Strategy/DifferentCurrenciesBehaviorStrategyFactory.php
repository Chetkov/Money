<?php

namespace Chetkov\Money\Strategy;

use Chetkov\Money\Exception\UnsupportedStrategyException;
use Chetkov\Money\Exchanger;

/**
 * Class DifferentCurrenciesBehaviorStrategyFactory
 * @package Chetkov\Money\Strategy
 */
class DifferentCurrenciesBehaviorStrategyFactory
{
    /**
     * @param string $strategyName
     * @return DifferentCurrenciesBehaviorStrategyInterface
     * @throws UnsupportedStrategyException
     */
    public static function create(string $strategyName): DifferentCurrenciesBehaviorStrategyInterface
    {
        switch ($strategyName) {
            case ErrorWhenCurrenciesAreDifferentStrategy::class:
                $strategy = new ErrorWhenCurrenciesAreDifferentStrategy();
                break;
            case SingleCurrencyConversionStrategy::class:
                $exchanger = Exchanger::getInstance();
                $strategy = new SingleCurrencyConversionStrategy($exchanger);
                break;
            default:
                throw new UnsupportedStrategyException($strategyName);
        }
        return $strategy;
    }
}
