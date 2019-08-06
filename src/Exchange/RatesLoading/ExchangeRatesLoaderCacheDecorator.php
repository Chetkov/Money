<?php

namespace Chetkov\Money\Exchange\RatesLoading;

/**
 * Class ExchangeRatesLoaderCacheDecorator
 * @package Chetkov\Money
 */
class ExchangeRatesLoaderCacheDecorator implements ExchangeRatesLoaderInterface
{
    /** @var ExchangeRatesLoaderInterface */
    private $exchangeRatesLoader;

    /** @var int */
    private $ttlInSeconds;

    /** @var int */
    private $ttlExpirationTime;

    /** @var array */
    private $rates = [];

    /**
     * ExchangeRatesLoaderCacheDecorator constructor.
     * @param ExchangeRatesLoaderInterface $exchangeRatesLoader
     * @param int $ttlInSeconds
     */
    public function __construct(ExchangeRatesLoaderInterface $exchangeRatesLoader, int $ttlInSeconds = 0)
    {
        $this->exchangeRatesLoader = $exchangeRatesLoader;
        $this->ttlInSeconds = $ttlInSeconds;
        $this->ttlExpirationTime = time();
    }

    /**
     * @param \DateTimeImmutable $dateTime
     * @return array
     */
    public function load(?\DateTimeImmutable $dateTime = null): array
    {
        if (!$this->rates || $this->ttlExpirationTime < time()) {
            $this->rates = $this->exchangeRatesLoader->load($dateTime);
            $this->ttlExpirationTime = time() + $this->ttlInSeconds;
        }
        return $this->rates;
    }
}
