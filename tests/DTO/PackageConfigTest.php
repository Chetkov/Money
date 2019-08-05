<?php

namespace Tests\Chetkov\Money\DTO;

use Chetkov\Money\DTO\PackageConfig;
use Chetkov\Money\Exception\RequiredParameterMissedException;
use Chetkov\Money\Strategy\ExchangeStrategyInterface;
use PHPUnit\Framework\TestCase;

/**
 * Class PackageConfigTest
 * @package Tests\Chetkov\Money\DTO
 */
class PackageConfigTest extends TestCase
{
    /** @var PackageConfig */
    private $config;

    /**
     * @throws RequiredParameterMissedException
     * @throws \ReflectionException
     */
    protected function setUp()
    {
        $reflectionClass = new \ReflectionClass(PackageConfig::getInstance());
        $instanceProperty = $reflectionClass->getProperty('instance');
        $instanceProperty->setAccessible(true);
        $instanceProperty->setValue(null);
        $instanceProperty->setAccessible(false);

        $config = require CHETKOV_MONEY_ROOT . '/config/example.config.php';
        $this->config = PackageConfig::getInstance($config);
    }

    public function testGetInstance(): void
    {
        $this->assertInstanceOf(PackageConfig::class, $this->config);
    }

    /**
     * @throws RequiredParameterMissedException
     */
    public function testReconfigure(): void
    {
        $this->config->reconfigure([
            'use_exchange_strategy' => true,
            'exchange_strategy_factory' => static function () {
                return $this->createMock(ExchangeStrategyInterface::class);
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
        $this->config->getExchangeStrategy();
        $this->assertTrue(true);
    }

    public function testUseExchangeStrategy(): void
    {
        $this->assertEquals(true, $this->config->useExchangeStrategy());
    }
}
