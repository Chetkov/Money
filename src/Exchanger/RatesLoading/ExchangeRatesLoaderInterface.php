<?php

namespace Chetkov\Money\Exchanger\RatesLoading;

/**
 * Interface ExchangeRatesLoaderInterface
 * @package Chetkov\Money\Exchanger\RatesLoading
 */
interface ExchangeRatesLoaderInterface
{
    /**
     * @param \DateTimeImmutable $dateTime
     * @return array
     */
    public function load(?\DateTimeImmutable $dateTime = null): array;
}