<?php

namespace Chetkov\Money;

use Chetkov\Money\DTO\PackageConfig;
use Chetkov\Money\Exception\CurrencyConversationStrategyIsNotSetException;
use Chetkov\Money\Strategy\CurrencyConversationStrategyInterface;

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

    /** @var CurrencyConversationStrategyInterface|null */
    private $currencyConversationStrategy;

    /**
     * Money constructor.
     * @param float $amount
     * @param string $currency
     * @param bool $useCurrencyConversationStrategy
     * @throws Exception\RequiredParameterMissedException
     */
    public function __construct(
        float $amount,
        string $currency,
        bool $useCurrencyConversationStrategy = false
    ) {
        $this->amount = $amount;
        $this->currency = $currency;

        $config = PackageConfig::getInstance();
        if ($useCurrencyConversationStrategy || $config->useCurrencyConversationStrategy()) {
            $this->currencyConversationStrategy = $config->getCurrencyConversationStrategy();
        }
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
     * @param Money $other
     * @return Money
     * @throws CurrencyConversationStrategyIsNotSetException
     * @throws Exception\RequiredParameterMissedException
     */
    public function add(self $other): self
    {
        $other = $this->convertToCurrentCurrency($other);
        return new Money($this->amount + $other->getAmount(), $this->currency);
    }

    /**
     * @param Money $other
     * @return Money
     * @throws CurrencyConversationStrategyIsNotSetException
     * @throws Exception\RequiredParameterMissedException
     */
    public function subtract(self $other): self
    {
        $other = $this->convertToCurrentCurrency($other);
        return new Money($this->amount - $other->getAmount(), $this->currency);
    }

    /**
     * @param float $factor
     * @return Money
     * @throws Exception\RequiredParameterMissedException
     */
    public function multiple(float $factor): self
    {
        return new Money($this->amount * $factor, $this->currency);
    }

    /**
     * @param int $n
     * @param int $precision
     * @return Money[]
     * @throws Exception\RequiredParameterMissedException
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
     * @throws Exception\RequiredParameterMissedException
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
     * @throws CurrencyConversationStrategyIsNotSetException
     */
    public function moreThan(self $other): bool
    {
        $other = $this->convertToCurrentCurrency($other);
        return $this->amount > $other->getAmount();
    }

    /**
     * @param Money $other
     * @return bool
     * @throws CurrencyConversationStrategyIsNotSetException
     */
    public function lessThan(self $other): bool
    {
        $other = $this->convertToCurrentCurrency($other);
        return $this->amount < $other->getAmount();
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return (string)json_encode([
            'amount' => $this->getAmount(),
            'currency' => $this->getCurrency(),
        ]);
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
     * @throws Exception\RequiredParameterMissedException
     */
    public static function fromJSON(string $json): self
    {
        $data = json_decode($json, true);
        $useCurrencyConversationStrategy = (bool)($data['currency_conversation_strategy'] ?? null);
        return new self($data['amount'], $data['currency'], $useCurrencyConversationStrategy);
    }

    /**
     * @param Money $other
     * @return Money
     * @throws CurrencyConversationStrategyIsNotSetException
     */
    private function convertToCurrentCurrency(Money $other): self
    {
        if ($this->currency === $other->getCurrency()) {
            return $other;
        }

        if (null === $this->currencyConversationStrategy) {
            throw new CurrencyConversationStrategyIsNotSetException();
        }

        return $this->currencyConversationStrategy->convert($other, $this);
    }
}
