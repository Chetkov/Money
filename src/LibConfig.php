<?php

namespace Chetkov\Money;

use Chetkov\Money\Exception\RequiredParameterMissedException;
use Chetkov\Money\Exchange\ExchangerInterface;

/**
 * Class LibConfig
 * @package Chetkov\Money
 */
class LibConfig
{
    /** @var self */
    private static $instance;

    /** @var array */
    private $config;

    /**
     * LibConfig constructor.
     * @param array $config
     * @throws RequiredParameterMissedException
     */
    private function __construct(array $config)
    {
        $this->reconfigure($config);
    }

    /**
     * @param array $config
     * @return LibConfig
     * @throws RequiredParameterMissedException
     */
    public static function getInstance(array $config = []): self
    {
        if (null === self::$instance) {
            self::$instance = new self($config);
        }
        return self::$instance;
    }

    /**
     * @param array $config
     * @throws RequiredParameterMissedException
     */
    public function reconfigure(array $config): void
    {
        $this->validate($config);
        $this->config = $config;
    }

    /**
     * @return bool
     */
    public function useCurrencyConversation(): bool
    {
        return $this->config['use_currency_conversation'];
    }

    /**
     * @return ExchangerInterface
     */
    public function getExchanger(): ExchangerInterface
    {
        return $this->config['exchanger_factory']();
    }

    /**
     * @param array $config
     * @throws RequiredParameterMissedException
     */
    private function validate(array $config): void
    {
        $requiredParameters = [
            'use_currency_conversation',
            'exchanger_factory',
        ];

        foreach ($requiredParameters as $requiredParameter) {
            if (!isset($config[$requiredParameter])) {
                throw new RequiredParameterMissedException($requiredParameter);
            }
        }
    }
}
