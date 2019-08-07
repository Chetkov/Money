<?php

namespace Chetkov\Money\Exchanger\RatesProvider;

/**
 * Class ExchangeRatesProviderCacheDecorator
 * @package Chetkov\Money\Exchanger\RatesProvider
 */
class ExchangeRatesProviderCacheDecorator implements ExchangeRatesProviderInterface
{
    private const DEFAULT_DATETIME_FORMAT = 'Y-m-d';

    /** @var ExchangeRatesProviderInterface */
    private $exchangeRatesProvider;

    /** @var int */
    private $ttlInSeconds;

    /** @var string */
    private $dateTimeFormat;

    /** @var int[] */
    private $ttlExpirationTimeLabels = [];

    /** @var array */
    private $rates = [];

    /**
     * ExchangeRatesProviderCacheDecorator constructor.
     * @param ExchangeRatesProviderInterface $exchangeRatesProvider
     * @param int $ttlInSeconds
     * @param string $dateTimeFormat
     */
    public function __construct(
        ExchangeRatesProviderInterface $exchangeRatesProvider,
        int $ttlInSeconds = 0,
        string $dateTimeFormat = self::DEFAULT_DATETIME_FORMAT
    ) {
        $this->exchangeRatesProvider = $exchangeRatesProvider;
        $this->ttlInSeconds = $ttlInSeconds;
        $this->dateTimeFormat = $dateTimeFormat;
    }

    /**
     * @param \DateTimeImmutable|null $dateTime
     * @return array
     */
    public function getRates(?\DateTimeImmutable $dateTime = null): array
    {
        $key = $dateTime ? $dateTime->format($this->dateTimeFormat) : '';
        if (!isset($this->rates[$key], $this->ttlExpirationTimeLabels[$key]) || $this->ttlExpirationTimeLabels[$key] < time()) {
            $this->rates[$key] = $this->exchangeRatesProvider->getRates($dateTime);
            $this->ttlExpirationTimeLabels[$key] = time() + $this->ttlInSeconds;
        }
        return $this->rates[$key];
    }
}
