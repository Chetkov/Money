<?php

namespace Chetkov\Money\Exchanger;

use Chetkov\DataStructures\Graph\Graph;
use Chetkov\DataStructures\Graph\PathFinder\ShortestPathFinder;
use Chetkov\Money\Exception\ExchangeRateWasNotFoundException;
use Chetkov\Money\Exception\RequiredParameterMissedException;
use Chetkov\Money\Exchanger\RatesProvider\ExchangeRatesProviderInterface;
use Chetkov\Money\Helper\CurrencyPairHelper;
use Chetkov\Money\Money;

/**
 * Class GraphRatesSearchingExchangerDecorator
 * @package Chetkov\Money\Exchanger
 */
class GraphRatesSearchingExchangerDecorator extends AbstractExchanger
{
    /** @var ExchangerInterface */
    private $exchanger;

    /**
     * GraphRatesSearchingExchangerDecorator constructor.
     * @param ExchangerInterface $exchanger
     * @param ExchangeRatesProviderInterface $exchangeRatesProvider
     */
    public function __construct(ExchangerInterface $exchanger, ExchangeRatesProviderInterface $exchangeRatesProvider)
    {
        parent::__construct($exchangeRatesProvider);
        $this->exchanger = $exchanger;
    }

    /**
     * @param Money $money
     * @param string $currency
     * @param int $roundingPrecision
     * @return Money
     * @throws ExchangeRateWasNotFoundException
     * @throws RequiredParameterMissedException
     */
    public function exchange(Money $money, string $currency, int $roundingPrecision = 2): Money
    {
        try {
            return $this->exchanger->exchange($money, $currency, $roundingPrecision);
        } catch (ExchangeRateWasNotFoundException $e) {
            return parent::exchange($money, $currency, $roundingPrecision);
        }
    }

    /**
     * @param Money $money
     * @param string $currency
     * @param array $exchangeRates
     * @return float
     * @throws ExchangeRateWasNotFoundException
     */
    protected function doExchange(Money $money, string $currency, array $exchangeRates): float
    {
        $exchangePath = $this->findExchangePath($money, $currency, $exchangeRates);
        $pathLength = count($exchangePath);

        if ($pathLength < 2) {
            $currencyPair = $money->getCurrency() . '-' . $currency;
            throw new ExchangeRateWasNotFoundException($currencyPair);
        }

        $exchangedAmount = $money->getAmount();
        for ($i = 0; $i < $pathLength - 1; $i++) {
            $currencyPair = CurrencyPairHelper::implode($exchangePath[$i], $exchangePath[$i + 1]);
            $reversePair = CurrencyPairHelper::reverse($currencyPair);
            switch (true) {
                case isset($exchangeRates[$currencyPair]):
                    $exchangedAmount *= $exchangeRates[$currencyPair];
                    break;
                case isset($exchangeRates[$reversePair]):
                    $exchangedAmount /= $exchangeRates[$reversePair];
                    break;
                default:
                    throw new ExchangeRateWasNotFoundException($currencyPair);
            }
        }

        return $exchangedAmount;
    }

    /**
     * @param Money $money
     * @param string $currency
     * @param array $exchangeRates
     * @return array
     */
    private function findExchangePath(Money $money, string $currency, array $exchangeRates): array
    {
        $currenciesGraph = $this->buildGraph($exchangeRates);
        $pathFinder = new ShortestPathFinder($currenciesGraph);
        return $pathFinder->findShortestPath($money->getCurrency(), $currency);
    }

    /**
     * @param array $exchangeRates
     * @return Graph
     */
    private function buildGraph(array $exchangeRates): Graph
    {
        $graph = new Graph();
        foreach ($exchangeRates as $currencyPair => $rate) {
            [$sellingCurrency, $purchasedCurrency] = CurrencyPairHelper::explode($currencyPair);
            $graph
                ->addNode($sellingCurrency)
                ->addNode($purchasedCurrency)
                ->addNodesLink($sellingCurrency, $purchasedCurrency, $rate, false)
                ->addNodesLink($purchasedCurrency, $sellingCurrency, 1/$rate, false);
        }
        return $graph;
    }
}
