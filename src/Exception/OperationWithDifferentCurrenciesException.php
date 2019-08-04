<?php

namespace Chetkov\Money\Exception;

use Throwable;

/**
 * Class OperationWithDifferentCurrenciesException
 * @package Chetkov\Money
 */
class OperationWithDifferentCurrenciesException extends MoneyException
{
    /**
     * OperationWithDifferentCurrenciesException constructor.
     * @param string $message
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct($message = 'Operation with instances in different currencies', $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
