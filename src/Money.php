<?php

namespace Chetkov\Money;

use Chetkov\Money\Exception\SerializationException;
use Chetkov\Money\Strategy\DifferentCurrenciesBehaviorStrategyFactory;
use Chetkov\Money\Strategy\DifferentCurrenciesBehaviorStrategyInterface;
use Chetkov\Money\Strategy\ErrorWhenCurrenciesAreDifferentStrategy;

/**
 * Class Money
 * @package Chetkov\Money
 */
class Money implements \JsonSerializable
{
    /** @var float */
    private $amount;

    /** @var string */
    private $currency;

    /** @var DifferentCurrenciesBehaviorStrategyInterface */
    private $differentCurrenciesBehaviorStrategy;

    /**
     * Money constructor.
     * @param float $amount
     * @param string $currency
     * @param string $differentCurrenciesBehaviorStrategy
     * @throws Exception\UnsupportedStrategyException
     */
    public function __construct(
        float $amount,
        string $currency,
        string $differentCurrenciesBehaviorStrategy = ErrorWhenCurrenciesAreDifferentStrategy::class
    ) {
        $this->amount = $amount;
        $this->currency = $currency;
        $this->differentCurrenciesBehaviorStrategy = DifferentCurrenciesBehaviorStrategyFactory::create($differentCurrenciesBehaviorStrategy);
    }

    /**
     * @return float
     */
    public function getAmount(): float
    {
        return $this->amount;
    }

    /**
     * @return string
     */
    public function getCurrency(): string
    {
        return $this->currency;
    }

    /**
     * @return string
     */
    public function getDifferentCurrencyBehaviorStrategy(): string
    {
        return get_class($this->differentCurrenciesBehaviorStrategy);
    }


    /**
     * @param Money $other
     * @return Money
     * @throws Exception\UnsupportedStrategyException
     */
    public function add(self $other): self
    {
        $other = $this->differentCurrenciesBehaviorStrategy->execute($other, $this->getCurrency());
        return new Money($this->amount + $other->getAmount(), $this->currency);
    }

    /**
     * @param Money $other
     * @return Money
     * @throws Exception\UnsupportedStrategyException
     */
    public function subtract(self $other): self
    {
        $other = $this->differentCurrenciesBehaviorStrategy->execute($other, $this->getCurrency());
        return new Money($this->amount - $other->getAmount(), $this->currency);
    }

    /**
     * @param float $factor
     * @return Money
     * @throws Exception\UnsupportedStrategyException
     */
    public function multiple(float $factor): self
    {
        return new Money($this->amount * $factor, $this->currency);
    }

    /**
     * @param int $n
     * @param int $precision
     * @return Money[]
     * @throws Exception\UnsupportedStrategyException
     */
    public function allocateEvenly(int $n, int $precision = 2): array
    {
        $result = [];
        $part = round($this->amount / $n, $precision);
        $balance = $this->amount;
        for ($i = 0; $i < $n - 1; $i++) {
            $result[] = new Money($part, $this->currency);
            $balance -= $part;
        }
        $result[] = new Money($balance, $this->currency);
        return $result;
    }

    /**
     * @param array $ratios
     * @param int $precision
     * @return Money[]
     * @throws Exception\UnsupportedStrategyException
     */
    public function allocateProportionally(array $ratios, int $precision = 2): array
    {
        $result = [];
        foreach ($ratios as $ratio) {
            $part = round($this->amount * $ratio, $precision);
            $result[] = new Money($part, $this->currency);
        }
        return $result;
    }

    /**
     * @param Money $other
     * @return bool
     */
    public function equals(self $other): bool
    {
        return $this->amount === $other->getAmount()
            && $this->currency === $other->getCurrency();
    }

    /**
     * @param Money $other
     * @return bool
     */
    public function moreThan(self $other): bool
    {
        $other = $this->differentCurrenciesBehaviorStrategy->execute($other, $this->getCurrency());
        return $this->amount > $other->getAmount();
    }

    /**
     * @param Money $other
     * @return bool
     */
    public function lessThan(self $other): bool
    {
        $other = $this->differentCurrenciesBehaviorStrategy->execute($other, $this->getCurrency());
        return $this->amount < $other->getAmount();
    }

    /**
     * @return string
     * @throws SerializationException
     */
    public function __toString(): string
    {
        $json = json_encode([
            'amount' => $this->getAmount(),
            'currency' => $this->getCurrency(),
            'different_currency_behavior_strategy' => $this->getDifferentCurrencyBehaviorStrategy()
        ]);

        if (!is_string($json)) {
            throw new SerializationException(json_last_error_msg());
        }

        return $json;
    }

    /**
     * @return string
     */
    public function jsonSerialize(): string
    {
        return (string)$this;
    }

    /**
     * @param string $json
     * @return Money
     * @throws Exception\UnsupportedStrategyException
     */
    public static function fromJSON(string $json): self
    {
        $data = json_decode($json, true);
        return isset($data['different_currency_behavior_strategy'])
            ? new self($data['amount'], $data['currency'], $data['different_currency_behavior_strategy'])
            : new self($data['amount'], $data['currency']);
    }
}
