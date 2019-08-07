<?php

namespace Tests\Chetkov\Money\Exchanger\RatesProvider;

use Chetkov\Money\Exchanger\RatesProvider\CbrExchangeRatesProvider;
use PHPUnit\Framework\TestCase;

/**
 * Class CbrExchangeRatesProviderTest
 * @package Tests\Chetkov\Money\Exchanger\RatesProvider
 */
class CbrExchangeRatesProviderTest extends TestCase
{
    /**
     * @throws \Exception
     */
    public function testLoad(): void
    {
        $ratesLoader = $this->createPartialMock(CbrExchangeRatesProvider::class, ['executeRequest']);

        $rawCbrXMLResponse = file_get_contents(__DIR__ . '/cbr-response.xml');
        $ratesLoader
            ->method('executeRequest')
            ->willReturn($rawCbrXMLResponse);

        /** @var CbrExchangeRatesProvider $ratesLoader */
        $rates = $ratesLoader->getRates();

        $resultRatesJSON = file_get_contents(__DIR__ . '/cbr-result-rates.json');
        $this->assertEquals(json_decode($resultRatesJSON, true), $rates);
    }
}
