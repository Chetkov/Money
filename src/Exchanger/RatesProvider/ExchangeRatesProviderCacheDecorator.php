<?php

namespace Chetkov\Money\Exchanger\RatesProvider;

/**
 * Class ExchangeRatesProviderCacheDecorator
 * @package Chetkov\Money\Exchanger\RatesProvider
 */
class ExchangeRatesProviderCacheDecorator implements ExchangeRatesProviderInterface
{
    /** @var ExchangeRatesProviderInterface */
    private $exchangeRatesProvider;

    /** @var int */
    private $ttlInSeconds;

    /** @var int */
    private $ttlExpirationTime;

    /** @var array */
    private $rates = [];

    /**
     * ExchangeRatesProviderCacheDecorator constructor.
     * @param ExchangeRatesProviderInterface $exchangeRatesProvider
     * @param int $ttlInSeconds
     */
    public function __construct(ExchangeRatesProviderInterface $exchangeRatesProvider, int $ttlInSeconds = 0)
    {
        $this->exchangeRatesProvider = $exchangeRatesProvider;
        $this->ttlInSeconds = $ttlInSeconds;
        $this->ttlExpirationTime = time();
    }

    /**
     * @param \DateTimeImmutable $dateTime
     * @return array
     */
    public function getRates(?\DateTimeImmutable $dateTime = null): array
    {
        if (!$this->rates || $this->ttlExpirationTime < time()) {
            $this->rates = $this->exchangeRatesProvider->getRates($dateTime);
            $this->ttlExpirationTime = time() + $this->ttlInSeconds;
        }
        return $this->rates;
    }
}
