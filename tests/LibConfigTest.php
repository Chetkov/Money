<?php

namespace Tests\Chetkov\Money;

use Chetkov\Money\Exception\RequiredParameterMissedException;
use Chetkov\Money\Exchanger\ExchangerInterface;
use Chetkov\Money\LibConfig;
use PHPUnit\Framework\TestCase;

/**
 * Class LibConfigTest
 * @package Tests\Chetkov\Money
 */
class LibConfigTest extends TestCase
{
    /** @var LibConfig */
    private $config;

    /**
     * @throws RequiredParameterMissedException
     * @throws \ReflectionException
     */
    protected function setUp()
    {
        $reflectionClass = new \ReflectionClass(LibConfig::getInstance());
        $instanceProperty = $reflectionClass->getProperty('instance');
        $instanceProperty->setAccessible(true);
        $instanceProperty->setValue(null);
        $instanceProperty->setAccessible(false);

        $config = require CHETKOV_MONEY_ROOT . '/config/example.config.php';
        $this->config = LibConfig::getInstance($config);
    }

    public function testGetInstance(): void
    {
        $this->assertInstanceOf(LibConfig::class, $this->config);
    }

    /**
     * @throws RequiredParameterMissedException
     */
    public function testReconfigure(): void
    {
        $this->config->reconfigure([
            'use_currency_conversation' => true,
            'exchanger_factory' => static function () {
                return $this->createMock(ExchangerInterface::class);
            },
        ]);
        $this->assertTrue(true);
    }

    /**
     * @throws RequiredParameterMissedException
     */
    public function testReconfigureNegative(): void
    {
        $this->expectException(RequiredParameterMissedException::class);
        $this->config->reconfigure([]);
    }

    public function testGetExchangeStrategy(): void
    {
        $this->config->getExchanger();
        $this->assertTrue(true);
    }

    public function testUseExchangeStrategy(): void
    {
        $this->assertEquals(true, $this->config->useCurrencyConversation());
    }
}
