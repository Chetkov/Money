<?php

namespace Chetkov\Money\Exchanger\RatesProvider\CacheDecorator\Strategy;

use Psr\SimpleCache\CacheInterface;

/**
 * Class ClassPropertyCacheStrategy
 * @package Chetkov\Money\Exchanger\RatesProvider\CacheDecorator\Strategy
 */
class ClassPropertyCacheStrategy implements CacheInterface
{
    /** @var array */
    private $storage = [];

    /** @var array */
    private $keyExpirationLabels = [];

    /**
     * @inheritDoc
     */
    public function get($key, $default = null)
    {
        return $this->has($key)
            ? $this->storage[$key]
            : $default;
    }

    /**
     * @inheritDoc
     * @throws \Exception
     */
    public function set($key, $value, $ttl = null): bool
    {
        switch (true) {
            case is_int($ttl):
                $this->keyExpirationLabels[$key] = time() + $ttl;
                break;
            case $ttl instanceof \DateInterval:
                $dateTime = new \DateTime();
                $dateTime->add($ttl);
                $this->keyExpirationLabels[$key] = $dateTime->getTimestamp();
                break;
            default:
                return false;
        }
        $this->storage[$key] = $value;
        return true;
    }

    /**
     * @inheritDoc
     */
    public function delete($key): bool
    {
        unset($this->storage[$key], $this->keyExpirationLabels[$key]);
        return true;
    }

    /**
     * @inheritDoc
     */
    public function clear(): bool
    {
        $this->storage = [];
        $this->keyExpirationLabels = [];
        return true;
    }

    /**
     * @inheritDoc
     */
    public function getMultiple($keys, $default = null): iterable
    {
        foreach ($keys as $key) {
            yield $this->get($key, $default);
        }
    }

    /**
     * @inheritDoc
     */
    public function setMultiple($values, $ttl = null): bool
    {
        $cachedKeys = [];
        foreach ($values as $key => $value) {
            if ($this->set($key, $value, $ttl)) {
                $cachedKeys[] = $key;
            } else {
                $this->deleteMultiple($cachedKeys);
                return false;
            }
        }
        return true;
    }

    /**
     * @inheritDoc
     */
    public function deleteMultiple($keys): bool
    {
        foreach ($keys as $key) {
            if (!$this->delete($key)) {
                return false;
            }
        }
        return true;
    }

    /**
     * @inheritDoc
     */
    public function has($key): bool
    {
        $valueIsSet = isset($this->storage[$key]);
        $keyIsExpired = isset($this->keyExpirationLabels[$key]) && $this->keyExpirationLabels[$key] < time();
        return $valueIsSet && !$keyIsExpired;
    }
}
