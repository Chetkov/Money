<?php

namespace Chetkov\Money\Exception;

use Throwable;

/**
 * Class CurrencyConversationStrategyIsNotSetException
 * @package Chetkov\Money\Exception
 */
class CurrencyConversationStrategyIsNotSetException extends MoneyException
{
    /**
     * CurrencyConversationStrategyIsNotSetException constructor.
     * @param string $message
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct(
        string $message = 'Unable to convert currencies, because CurrencyConversationStrategy is not set!',
        int $code = 0,
        Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
