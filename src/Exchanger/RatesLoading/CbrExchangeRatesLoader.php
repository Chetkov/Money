<?php

namespace Chetkov\Money\Exchanger\RatesLoading;

use Chetkov\Money\CurrencyEnum;
use Chetkov\Money\Exception\MoneyException;

/**
 * Class CbrExchangeRatesLoader
 * @package Chetkov\Money\Exchanger\RatesLoading
 */
class CbrExchangeRatesLoader implements ExchangeRatesLoaderInterface
{
    private const API_URL = 'http://www.cbr.ru/scripts/XML_daily.asp?date_req=';
    private const DATE_TIME_FORMAT = 'd/m/Y';

    /**
     * @param \DateTimeImmutable|null $dateTime
     * @return array
     * @throws \Exception
     */
    public function load(?\DateTimeImmutable $dateTime = null): array
    {
        $result = [];

        $dom = new \DOMDocument();
        $dom->loadXML($this->executeRequest($dateTime));
        /** @var \DOMElement $valuteElement */
        foreach ($dom->getElementsByTagName('Valute') as $valuteElement) {
            $currencyCode = $valuteElement->getElementsByTagName('CharCode')[0]->nodeValue;
            $rate = $valuteElement->getElementsByTagName('Value')[0]->nodeValue;

            $currencyPair = CurrencyEnum::getCurrencyPairCode($currencyCode, CurrencyEnum::RUB);
            $rate = (float)str_replace(',', '.', $rate);
            $result[$currencyPair] = $rate;
        }

        return $result;
    }

    /**
     * @param \DateTimeImmutable|null $dateTime
     * @return string
     * @throws \Exception
     */
    protected function executeRequest(?\DateTimeImmutable $dateTime = null): string
    {
        $dateTime = $dateTime ?? new \DateTimeImmutable();
        $apiUrl = self::API_URL . $dateTime->format(self::DATE_TIME_FORMAT);

        $content = file_get_contents($apiUrl);
        if ($content === false) {
            throw new MoneyException('Error of exchange rates getting. Loader: ' . get_class($this));
        }

        return $content;
    }
}
