<?php

namespace Chetkov\Money\Exception;

use Throwable;

/**
 * Class ExchangeRateWasNotFoundException
 * @package Chetkov\Money\Exception
 */
class ExchangeRateWasNotFoundException extends MoneyException
{
    /**
     * ExchangeRateWasNotFoundException constructor.
     * @param string $currencyPair
     * @param string $message
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct(string $currencyPair, $message = '', $code = 0, Throwable $previous = null)
    {
        if (!$message) {
            $message = "Exchange rate for currency pair $currencyPair was not found";
        }
        parent::__construct($message, $code, $previous);
    }
}
