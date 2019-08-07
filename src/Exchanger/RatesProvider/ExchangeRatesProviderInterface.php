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
     *
     * @example ['USD-RUB' => [66.34, 68.21]]
     * @example ['USD-RUB' => [67.31]]
     */
    public function getRates(?\DateTimeImmutable $dateTime = null): array;
}