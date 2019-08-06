<?php

namespace Chetkov\Money;

use Chetkov\Money\Exchange\ExchangerInterface;

/**
 * Class Money
 * @package Chetkov\Money
 * @method static Money RUB(float $amount = 0.0)
 * @method static Money AUD(float $amount = 0.0)
 * @method static Money AZN(float $amount = 0.0)
 * @method static Money GBP(float $amount = 0.0)
 * @method static Money AMD(float $amount = 0.0)
 * @method static Money BYN(float $amount = 0.0)
 * @method static Money BGN(float $amount = 0.0)
 * @method static Money BRL(float $amount = 0.0)
 * @method static Money HUF(float $amount = 0.0)
 * @method static Money HKD(float $amount = 0.0)
 * @method static Money DKK(float $amount = 0.0)
 * @method static Money USD(float $amount = 0.0)
 * @method static Money EUR(float $amount = 0.0)
 * @method static Money INR(float $amount = 0.0)
 * @method static Money KZT(float $amount = 0.0)
 * @method static Money CAD(float $amount = 0.0)
 * @method static Money KGS(float $amount = 0.0)
 * @method static Money CNY(float $amount = 0.0)
 * @method static Money MDL(float $amount = 0.0)
 * @method static Money NOK(float $amount = 0.0)
 * @method static Money PLN(float $amount = 0.0)
 * @method static Money RON(float $amount = 0.0)
 * @method static Money XDR(float $amount = 0.0)
 * @method static Money SGD(float $amount = 0.0)
 * @method static Money TJS(float $amount = 0.0)
 * @method static Money TRY(float $amount = 0.0)
 * @method static Money TMT(float $amount = 0.0)
 * @method static Money UZS(float $amount = 0.0)
 * @method static Money UAH(float $amount = 0.0)
 * @method static Money CZK(float $amount = 0.0)
 * @method static Money SEK(float $amount = 0.0)
 * @method static Money CHF(float $amount = 0.0)
 * @method static Money ZAR(float $amount = 0.0)
 * @method static Money KRW(float $amount = 0.0)
 * @method static Money JPY(float $amount = 0.0)
 */
class Money implements \JsonSerializable
{
    /** @var float */
    private $amount;

    /** @var string */
    private $currency;

    /** @var ExchangerInterface|null */
    private $exchanger;

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
        bool $useCurrencyConversationStrategy = false //TODO: нужен-ли? или опираться только на конфиг?
    ) {
        $this->amount = $amount;
        $this->currency = $currency;

        $config = LibConfig::getInstance();
        if ($useCurrencyConversationStrategy || $config->useCurrencyConversation()) {
            $this->exchanger = $config->getExchanger();
        }
    }

    /**
     * @param string $name
     * @param array $arguments
     * @return Money
     * @throws Exception\RequiredParameterMissedException
     */
    public static function __callStatic(string $name, array $arguments): Money
    {
        $currency = $name;
        $amount = $arguments[0] ?? 0.0;
        return new self($amount, $currency);
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
     * @throws Exception\ExchangeRateWasNotFoundException
     * @throws Exception\OperationWithDifferentCurrenciesException
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
     * @throws Exception\ExchangeRateWasNotFoundException
     * @throws Exception\OperationWithDifferentCurrenciesException
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
     * @param int $roundingPrecision
     * @return Money[]
     * @throws Exception\RequiredParameterMissedException
     */
    public function allocateEvenly(int $n, int $roundingPrecision = 2): array
    {
        $result = [];
        $part = round($this->amount / $n, $roundingPrecision);
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
     * @param int $roundingPrecision
     * @return Money[]
     * @throws Exception\RequiredParameterMissedException
     */
    public function allocateProportionally(array $ratios, int $roundingPrecision = 2): array
    {
        $result = [];
        foreach ($ratios as $ratio) {
            $part = round($this->amount * $ratio, $roundingPrecision);
            $result[] = new Money($part, $this->currency);
        }
        return $result;
    }

    /**
     * @param Money $other
     * @param bool $isCrossCurrencyComparison
     * @param float $allowableDeviationPercent
     * @return bool
     * @throws Exception\ExchangeRateWasNotFoundException
     * @throws Exception\OperationWithDifferentCurrenciesException
     */
    public function equals(self $other, bool $isCrossCurrencyComparison = false, float $allowableDeviationPercent = 0): bool
    {
        if ($isCrossCurrencyComparison) {
            $other = $this->convertToCurrentCurrency($other);
            $deviation = abs($this->getAmount() - $other->getAmount());
            $deviationPercentInFact = $deviation / $this->getAmount() * 100;
            return $deviationPercentInFact < $allowableDeviationPercent;
        }

        return $this->amount === $other->getAmount()
            && $this->currency === $other->getCurrency();
    }

    /**
     * @param Money $other
     * @return bool
     * @throws Exception\ExchangeRateWasNotFoundException
     * @throws Exception\OperationWithDifferentCurrenciesException
     */
    public function moreThan(self $other): bool
    {
        $other = $this->convertToCurrentCurrency($other);
        return $this->amount > $other->getAmount();
    }

    /**
     * @param Money $other
     * @return bool
     * @throws Exception\ExchangeRateWasNotFoundException
     * @throws Exception\OperationWithDifferentCurrenciesException
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
        return new self($data['amount'], $data['currency']);
    }

    /**
     * @param Money $other
     * @return Money
     * @throws Exception\ExchangeRateWasNotFoundException
     * @throws Exception\OperationWithDifferentCurrenciesException
     */
    private function convertToCurrentCurrency(Money $other): self
    {
        if ($this->currency === $other->getCurrency()) {
            return $other;
        }

        if (null === $this->exchanger) {
            throw new Exception\OperationWithDifferentCurrenciesException();
        }

        return $this->exchanger->exchange($other, $this->getCurrency());
    }
}
