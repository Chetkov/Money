<?php

namespace Chetkov\Money\Exchange\RatesLoading;

/**
 * Interface ExchangeRatesLoaderInterface
 * @package Chetkov\Money
 */
interface ExchangeRatesLoaderInterface
{
    /**
     * @param \DateTimeImmutable $dateTime
     * @return array
     */
    public function load(?\DateTimeImmutable $dateTime = null): array;
}