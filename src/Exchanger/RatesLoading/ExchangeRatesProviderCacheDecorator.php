<?php

namespace Chetkov\Money\Exchanger\RatesLoading;

/**
 * Class ExchangeRatesProviderCacheDecorator
 * @package Chetkov\Money\Exchanger\RatesLoading
 */
class ExchangeRatesProviderCacheDecorator implements ExchangeRatesProviderInterface
{
    /** @var ExchangeRatesProviderInterface */
    private $exchangeRatesLoader;

    /** @var int */
    private $ttlInSeconds;

    /** @var int */
    private $ttlExpirationTime;

    /** @var array */
    private $rates = [];

    /**
     * ExchangeRatesProviderCacheDecorator constructor.
     * @param ExchangeRatesProviderInterface $exchangeRatesLoader
     * @param int $ttlInSeconds
     */
    public function __construct(ExchangeRatesProviderInterface $exchangeRatesLoader, int $ttlInSeconds = 0)
    {
        $this->exchangeRatesLoader = $exchangeRatesLoader;
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
            $this->rates = $this->exchangeRatesLoader->getRates($dateTime);
            $this->ttlExpirationTime = time() + $this->ttlInSeconds;
        }
        return $this->rates;
    }
}
