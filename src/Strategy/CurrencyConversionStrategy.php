<?php

namespace Chetkov\Money\Strategy;

use Chetkov\Money\Exception\ExchangeRateWasNotFoundException;
use Chetkov\Money\ExchangerInterface;
use Chetkov\Money\Money;

/**
 * Class CurrencyConversionStrategy
 * @package Chetkov\Money\Strategy
 */
class CurrencyConversionStrategy implements CurrencyConversationStrategyInterface
{
    /** @var ExchangerInterface */
    private $exchanger;

    /**
     * CurrencyConversionStrategy constructor.
     * @param ExchangerInterface $exchanger
     */
    public function __construct(ExchangerInterface $exchanger)
    {
        $this->exchanger = $exchanger;
    }

    /**
     * @param Money $other
     * @param Money $current
     * @return Money
     * @throws ExchangeRateWasNotFoundException
     */
    public function convert(Money $other, Money $current): Money
    {
        if ($other->getCurrency() !== $current->getCurrency()) {
            $exchangedMoney = $this->exchanger->exchange($other, $current->getCurrency());
            $exchangedMoney->setCurrencyConversationStrategy($current->getCurrencyConversationStrategy());
            return $exchangedMoney;
        }
        return $other;
    }
}
