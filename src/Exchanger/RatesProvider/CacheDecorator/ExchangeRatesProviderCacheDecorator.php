<?php

namespace Chetkov\Money\Exchanger\RatesProvider\CacheDecorator;

use Chetkov\Money\Exchanger\RatesProvider\ExchangeRatesProviderInterface;
use Psr\SimpleCache\CacheInterface;
use Psr\SimpleCache\InvalidArgumentException;

/**
 * Class ExchangeRatesProviderCacheDecorator
 * @package Chetkov\Money\Exchanger\RatesProvider\CacheDecorator
 */
class ExchangeRatesProviderCacheDecorator implements ExchangeRatesProviderInterface
{
    private const DEFAULT_DATETIME_FORMAT = 'Y-m-d';
    private const DEFAULT_CACHE_KEY_PREFIX = 'cm_exchange_rates';

    /** @var ExchangeRatesProviderInterface */
    protected $exchangeRatesProvider;

    /** @var CacheInterface */
    private $cacheStrategy;

    /** @var string */
    private $cacheKeyPrefix;

    /** @var int */
    protected $ttlInSeconds;

    /** @var string */
    protected $dateTimeFormat;

    /**
     * ExchangeRatesProviderSecondLevelCacheDecorator constructor.
     * @param ExchangeRatesProviderInterface $exchangeRatesProvider
     * @param CacheInterface $cacheStrategy
     * @param int $ttlInSeconds
     * @param string $dateTimeFormat
     * @param string $cacheKeyPrefix
     */
    public function __construct(
        ExchangeRatesProviderInterface $exchangeRatesProvider,
        CacheInterface $cacheStrategy,
        int $ttlInSeconds = 0,
        string $dateTimeFormat = self::DEFAULT_DATETIME_FORMAT,
        string $cacheKeyPrefix = self::DEFAULT_CACHE_KEY_PREFIX
    ) {
        $this->exchangeRatesProvider = $exchangeRatesProvider;
        $this->ttlInSeconds = $ttlInSeconds;
        $this->dateTimeFormat = $dateTimeFormat;
        $this->cacheStrategy = $cacheStrategy;
        $this->cacheKeyPrefix = $cacheKeyPrefix;
    }

    /**
     * @param \DateTimeImmutable $dateTime
     * @return array
     * @throws InvalidArgumentException
     *
     * @example ['USD-RUB' => [66.34, 68.21]]
     * @example ['USD-RUB' => [67.31]]
     */
    public function getRates(?\DateTimeImmutable $dateTime = null): array
    {
        $cacheKey = $this->getCacheKey($dateTime);
        $rates = $this->cacheStrategy->get($cacheKey);
        if (!$rates) {
            $rates = $this->exchangeRatesProvider->getRates($dateTime);
            $this->cacheStrategy->set($cacheKey, $rates, $this->ttlInSeconds);
        }
        return $rates;
    }

    /**
     * @param \DateTimeImmutable $dateTime
     * @return string
     */
    public function getCacheKey(?\DateTimeImmutable $dateTime): string
    {
        $dateTimeKey = $dateTime ? $dateTime->format($this->dateTimeFormat) : '';
        return "$this->cacheKeyPrefix:$dateTimeKey";
    }
}
