<?php

namespace Chetkov\Money\Exchanger\RatesLoading;

/**
 * Interface ExchangeRatesProviderInterface
 * @package Chetkov\Money\Exchanger\RatesLoading
 */
interface ExchangeRatesProviderInterface
{
    /**
     * @param \DateTimeImmutable $dateTime
     * @return array
     */
    public function getRates(?\DateTimeImmutable $dateTime = null): array;
}