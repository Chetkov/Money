<?php

namespace Tests\Chetkov\Money\Exchange\RatesLoading;

use Chetkov\Money\Exchange\RatesLoading\CbrExchangeRatesLoader;
use PHPUnit\Framework\TestCase;

/**
 * Class CbrExchangeRatesLoaderTest
 * @package Tests\Chetkov\Money\Exchange\RatesLoading
 */
class CbrExchangeRatesLoaderTest extends TestCase
{
    /**
     * @throws \Exception
     */
    public function testLoad(): void
    {
        $ratesLoader = $this->createPartialMock(CbrExchangeRatesLoader::class, ['executeRequest']);

        $rawCbrXMLResponse = file_get_contents(__DIR__ . '/cbr-response.xml');
        $ratesLoader
            ->method('executeRequest')
            ->willReturn($rawCbrXMLResponse);

        /** @var CbrExchangeRatesLoader $ratesLoader */
        $rates = $ratesLoader->load();

        $resultRatesJSON = file_get_contents(__DIR__ . '/cbr-result-rates.json');
        $this->assertEquals(json_decode($resultRatesJSON, true), $rates);
    }
}
