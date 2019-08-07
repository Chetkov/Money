<?php

namespace Chetkov\Money\Exchanger\RatesProvider;

/**
 * Interface ExchangeRatesProviderInterface
 * @package Chetkov\Money\Exchanger\RatesProvider
 */
interface ExchangeRatesProviderInterface
{
    /**
     * @param \DateTimeImmutable $dateTime
     * @return array
     */
    public function getRates(?\DateTimeImmutable $dateTime = null): array;
}