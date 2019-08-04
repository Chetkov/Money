<?php

namespace Chetkov\Money\DTO;

use Chetkov\Money\Exception\RequiredParameterMissedException;
use Chetkov\Money\Strategy\CurrencyConversationStrategyInterface;

/**
 * Class PackageConfig
 * @package Chetkov\Money\DTO
 */
class PackageConfig
{
    /** @var self */
    private static $instance;

    /** @var array */
    private $config;

    /**
     * PackageConfig constructor.
     * @param array $config
     * @throws RequiredParameterMissedException
     */
    private function __construct(array $config)
    {
        $this->reconfigure($config);
    }

    /**
     * @param array $config
     * @return PackageConfig
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
    public function useCurrencyConversationStrategy(): bool
    {
        return $this->config['use_currency_conversation_strategy'];
    }

    /**
     * @return CurrencyConversationStrategyInterface
     */
    public function getCurrencyConversationStrategy(): CurrencyConversationStrategyInterface
    {
        return $this->config['currency_conversation_strategy_factory']();
    }

    /**
     * @param array $config
     * @throws RequiredParameterMissedException
     */
    private function validate(array $config): void
    {
        $requiredParameters = [
            'use_currency_conversation_strategy',
            'currency_conversation_strategy_factory',
        ];

        foreach ($requiredParameters as $requiredParameter) {
            if (!isset($config[$requiredParameter])) {
                throw new RequiredParameterMissedException($requiredParameter);
            }
        }
    }
}
