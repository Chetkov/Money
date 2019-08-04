<?php

namespace Chetkov\Money\Exception;

use Throwable;

/**
 * Class RequiredParameterMissedException
 * @package Chetkov\Money\Exception
 */
class RequiredParameterMissedException extends MoneyException
{
    /**
     * RequiredParameterMissedException constructor.
     * @param string $parameter
     * @param string $message
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct(string $parameter, $message = '', $code = 0, Throwable $previous = null)
    {
        if (!$message) {
            $message = "Required parameter $parameter missed";
        }
        parent::__construct($message, $code, $previous);
    }
}
