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
    private $libConfig;

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

        LibConfigurator::configureForTests();
        $this->libConfig = LibConfig::getInstance();
    }

    public function testGetInstance(): void
    {
        $this->assertInstanceOf(LibConfig::class, $this->libConfig);
    }

    /**
     * @throws RequiredParameterMissedException
     */
    public function testReconfigure(): void
    {
        $this->libConfig->reconfigure([
            'is_currency_conversation_enabled' => true,
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
        $this->libConfig->reconfigure([]);
    }

    public function testGetExchangeStrategy(): void
    {
        $this->libConfig->getExchanger();
        $this->assertTrue(true);
    }

    public function testUseExchangeStrategy(): void
    {
        $this->assertEquals(true, $this->libConfig->isCurrencyConversationEnabled());
    }
}
