#### Описание:
Пакет реализован для упрощения работы с денежными значениями. 

Предоставляет возможность:
- сложения, вычетания, умножения денежных значений;
- равномерного распределения денежного значения на N частей;
- пропорционального распределения денежного значения в соответствии с заданным соотношением;
- конвертации валют;
- сравнения деннежных значений между собой;
- выполнения всех выше перечисленных операций для денежных значений в разных валютах;

#### Установка:
```shell script
composer require v.chetkov/money
```

#### Конфигурация
_example.config.php_
```php
<?php 

use Chetkov\Money\Exchanger\ExchangerInterface;
use Chetkov\Money\Exchanger\RatesProvider\SimpleExchangeRatesProvider;
use Chetkov\Money\Exchanger\SimpleExchanger;

$exchangeRates = [
    'USD-RUB' => 66.34,
    'EUR-RUB' => 72.42,
    'JPY-RUB' => 0.61,
];

return [
    'use_currency_conversation' => true,
    'exchanger_factory' => static function () use ($exchangeRates): ExchangerInterface {
        //Фабрика класса обменника
        static $instance;
        if (null === $instance) {
            $ratesLoader = SimpleExchangeRatesProvider::getInstance($exchangeRates);
            $instance = new SimpleExchanger($ratesLoader);
        }
        return $instance;
    },
];
```
Если параметер `'use_currency_conversation' => false`, то при выполнении операций с экземплярами _Money_ в разных валютах будет выброшено исключение _OperationWithDifferentCurrenciesException_.

В противном случае работа будет передана обменнику (о них чуть-чуть позже) для приведения второго операнда к валюте первого.

Хранением, обновлением и предоставлением обменнику курсов валют занимаются классы поставщики, реализующие _ExchangeRatesProviderInterface_.  


#### Обменники
Вы можете использовать существующие классы обменников: 

1 - _SimpleExchanger_
- получает курсы валют от поставщика;
- осуществляет поиск нужной валютной пары (в случае отсутствия в списке бросает исключение _ExchangeRateWasNotFoundException_);
- выполняет обмен и возвращает новый экземпляр _Money_;

2 - _GraphRatesSearchingExchangerDecorator_
- декорирует любой другой класс обменника;
- сперва делегирует работу декорируемому объекту и ловит исключение _ExchangeRateWasNotFoundException_;
- если тот справился с задачей самостоятельно, возвращает полученное значение;
- в противном случае строит граф из имеющихся валютных пар и пытается найти путь обмена через другие валюты, по сути 2-ой, 3-ой ... n-ый обмен (если безуспешно, бросает исключение _ExchangeRateWasNotFoundException_);

Или воплотить в жизнь собственные реализации.

#### Поставщики курсов валют
1 - _SimpleExchangeRatesProvider_ 
- реализует шаблон Singleton;
- принимает массив с курсами валют;
- предоставляет метод установки курса для новых валютных пар;

Самый примитивный пример: Инстанциировать и выполнить загрузку курсов валют в bootstrap файле Вашего приложения. 

Вы можете единожды загрузить курсы из вашей БД. 
Можете сделать механизм получения курсов со стороннего ресурса (допустим с валютной биржи).
Можете обновлять данные с заданым интервалом в ваших воркерах. Как всегда это зависит от ситуации, решение за Вами ;)

2 - _CbrExchangeRatesProvider_
- ходит в API ЦБ за курсами на нужную дату;

3 - _ExchangeRatesProviderCacheDecorator_
- декорирует любой другой класс постващика;
- кэширует в свойство список курсов, полученный от декорируемого объекта;
- следит за TTL, регулирует процесс актуализацию списка;

```php
<?php 

use Chetkov\Money\Exchanger\RatesProvider\CbrExchangeRatesProvider;
use Chetkov\Money\Exchanger\RatesProvider\ExchangeRatesProviderCacheDecorator;

$ratesProvider = new CbrExchangeRatesProvider();
$cachingDecorator = new ExchangeRatesProviderCacheDecorator($ratesProvider, 60);

$cachingDecorator->getRates(); // Получает и возвращает ставки от оригинального поставщика
sleep(55);
$cachingDecorator->getRates(); // Возвращает ставки из кэша
sleep(10);
$cachingDecorator->getRates(); // Получает и возвращает ставки от оригинального поставщика
```

Аналогично обменникам Вы можете делать собственные реализации поставщиков. 

#### Использование
Далее необходимо загрузить описанный выше конфиг в _PackageConfig_, это необходимо для понимания:
1) включена-ли автоматическая конвертация валют
2) какой обменник за это отвечает
3) какой поставщик предоставляет ему данные
```php
<?php 

use Chetkov\Money\LibConfig;

$config = require __DIR__ . 'config/example.config.php';

LibConfig::getInstance($config);
```

после чего можем выполнять различные операции:
```php
<?php

use Chetkov\Money\Money;

$moneyInUSD = Money::USD(100);
$moneyInRUB = Money::RUB(200);
```

###### Add:
```php
$additionResult = $moneyInUSD->add($moneyInRUB);
echo $additionResult; 
// Result: {"amount":103.01,"currency":"USD"}
```

###### Subtract:
```php
$subtractionResult = $moneyInRUB->subtract($moneyInUSD);
echo $subtractionResult; 
// Result: {"amount":-6434,"currency":"RUB"}
```

###### Multiply:
```php
$multiplicationResult = $moneyInRUB->multiple(5);
echo $multiplicationResult; 
// Result: {"amount":1000,"currency":"RUB"}
```

###### AllocateEvenly:
```php
$evenlyAllocationResult = $moneyInUSD->allocateEvenly(4);
echo json_encode($evenlyAllocationResult);
// Result: 
// [
//     {"amount":25,"currency":"USD"},
//     {"amount":25,"currency":"USD"},
//     {"amount":25,"currency":"USD"},
//     {"amount":25,"currency":"USD"}
// ]
```

Вы можете передать точность округления (опционально):
```php
$evenlyAllocationResult = $moneyInUSD->allocateEvenly(3, 4);
echo json_encode($evenlyAllocationResult);
// Result: 
// [
//     {"amount":33.3333,"currency":"USD"},
//     {"amount":33.3333,"currency":"USD"},
//     {"amount":33.3334,"currency":"USD"}
// ]
```

###### AllocateProportionally:
```php
$proportionallyAllocationResult = $moneyInUSD->allocateProportionally([0.18, 0.32, 0.5, 0.3, 1]);
echo json_encode($proportionallyAllocationResult);
// Result: 
// [
//     {"amount":18,"currency":"USD"},
//     {"amount":32,"currency":"USD"},
//     {"amount":50,"currency":"USD"},
//     {"amount":30,"currency":"USD"},
//     {"amount":100,"currency":"USD"}
// ]
```

###### LessThan
```php
$moneyInRUB->lessThan($moneyInUSD); // true
```

###### MoreThan
```php
$moneyInUSD->moreThan($moneyInRUB); // true
```

###### Equals
```php
$moneyInUSD->equals($moneyInRUB); // false
```

Или кросс-валютная проверка на равенство/относительное равенство.

- $isCrossCurrenciesComparison - флаг кросс-валютного сравнения (bool)
- $allowableDeviationPercent - допустимый процент отклонения (float: 0.0 .. 100.0)
```php
$moneyInRUB = Money::RUB(200);
$moneyInUSD = Money::USD(3.015);

$isCrossCurrenciesComparison = true;
$moneyInRUB->equals($moneyInUSD, $isCrossCurrenciesComparison); // false

$allowableDeviationPercent = 0.5;
$moneyInRUB->equals($moneyInUSD, $isCrossCurrenciesComparison, $allowableDeviationPercent); // true
```

#### PS:
Пока на этом всё, но я думаю в скором времени пакет увидит еще множество доработок. По мере развития буду стараться поддерживать README в актуальном состоянии.

#### PPS:
Идея была взята из книги Мартина Фаулера: "Шаблоны корпоративных приложений". 