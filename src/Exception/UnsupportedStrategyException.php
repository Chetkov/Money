<?php

namespace Chetkov\Money\Exception;

use Throwable;

/**
 * Class UnsupportedStrategyException
 * @package Chetkov\Money\Exception
 */
class UnsupportedStrategyException extends MoneyException
{
    /**
     * UnsupportedStrategyException constructor.
     * @param string $strategyName
     * @param string $message
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct(string $strategyName, $message = '', $code = 0, Throwable $previous = null)
    {
        if (!$message) {
            $message = "Unsupported strategy: $strategyName";
        }
        parent::__construct($message, $code, $previous);
    }
}
