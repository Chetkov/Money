#### Описание:
Пакет реализован для упрощения работы с денежными значениями. 

Предоставляет возможность:
- сложения, вычетания, умножения денежных значений;
- равномерного распределения денежного значения на N частей;
- пропорционального распределения денежного значения в соответствии с заданным соотношением;
- конвертации валют;
- сравнения деннежных значений между собой (с возможностью указать допустимый процент отклонения)
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
    'USD-RUB' => [66.34, 68.12], // Курсы покупки/продажи отличаются
    'EUR-RUB' => [72.42],        // Единый курс 
    'JPY-RUB' => [0.61],         // ...
];

return [
    'use_currency_conversation' => true,
    'exchanger_factory' => static function () use ($exchangeRates): ExchangerInterface {
        //Фабрика класса обменника
        static $instance;
        if (null === $instance) {
            $ratesProvider = SimpleExchangeRatesProvider::getInstance($exchangeRates);
            $instance = new SimpleExchanger($ratesProvider);
        }
        return $instance;
    },
];
```
Если параметер `'use_currency_conversation' => false`, то при выполнении операций с экземплярами _Money_ в разных валютах будет выброшено исключение _OperationWithDifferentCurrenciesException_.

В противном случае работа будет передана обменнику (о них чуть-чуть позже) для приведения второго операнда к валюте первого.

Хранением и предоставлением обменнику курсов валют занимаются классы поставщики, реализующие _ExchangeRatesProviderInterface_.  


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

$usd = Money::USD(100);
$rub = Money::RUB(200);
```

###### Exchange:
```php
echo $usd->exchange(CurrencyEnum::RUB);
// Result: {"amount":6634,"currency":"RUB"}
```

###### Add:
```php
$additionResult = $usd->add($rub);
echo $additionResult; 
// Result: {"amount":103.01,"currency":"USD"}
```

###### Subtract:
```php
$subtractionResult = $rub->subtract($usd);
echo $subtractionResult; 
// Result: {"amount":-6434,"currency":"RUB"}
```

###### Multiply:
```php
$multiplicationResult = $rub->multiple(5);
echo $multiplicationResult; 
// Result: {"amount":1000,"currency":"RUB"}
```

###### AllocateEvenly:
```php
$evenlyAllocationResult = $usd->allocateEvenly(4);
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
$evenlyAllocationResult = $usd->allocateEvenly(3, 4);
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
$proportionallyAllocationResult = $usd->allocateProportionally([0.18, 0.32, 0.5, 0.3, 1]);
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
$rub->lessThan($usd); // true
```

###### MoreThan
```php
$usd->moreThan($rub); // true
```

###### Equals
```php
$usd->equals($rub); // false
```

Или кросс-валютная проверка на равенство/относительное равенство.

- $isCrossCurrenciesComparison - флаг кросс-валютного сравнения (bool)
- $allowableDeviationPercent - допустимый процент отклонения (float: 0.0 .. 100.0)
```php
$rub = Money::RUB(200);
$usd = Money::USD(3.015);

$isCrossCurrenciesComparison = true;
$rub->equals($usd, $isCrossCurrenciesComparison); // false

$allowableDeviationPercent = 0.5;
$rub->equals($usd, $isCrossCurrenciesComparison, $allowableDeviationPercent); // true
```


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
- предоставляет метод установки курсов для новых валютных пар;

Самый примитивный пример: Инстанциировать и выполнить загрузку курсов валют в bootstrap файле Вашего приложения. 

Вы можете единожды загрузить курсы из вашей БД. 
Можете сделать механизм получения курсов со стороннего ресурса (допустим с валютной биржи).
Можете обновлять данные с заданым интервалом в ваших воркерах. Как всегда это зависит от ситуации, решение за Вами ;)

2 - _CbrExchangeRatesProvider_
- ходит в API ЦБ за курсами на указанную дату;

3 - _ExchangeRatesProviderCacheDecorator_
- декорирует любой другой класс постващика;
- может работать с разными стратегиями кэширования (стратегия должна реализовывать Psr\SimpleCache\CacheInterface) 
- следит за TTL и регулирует процесс актуализации списка;

```php
<?php 

use Chetkov\Money\Exchanger\RatesProvider\CacheDecorator\ExchangeRatesProviderCacheDecorator;
use Chetkov\Money\Exchanger\RatesProvider\CacheDecorator\Strategy\ClassPropertyCacheStrategy;
use Chetkov\Money\Exchanger\RatesProvider\CbrExchangeRatesProvider;

$ratesProvider = new CbrExchangeRatesProvider();
$cacheStrategy = new ClassPropertyCacheStrategy();
$cachingDecorator = new ExchangeRatesProviderCacheDecorator($ratesProvider, $cacheStrategy, 60);

$cachingDecorator->getRates(); // Получает, кэширует и возвращает ставки от оригинального поставщика
sleep(55);
$cachingDecorator->getRates(); // Возвращает ставки из кэша
sleep(10);
$cachingDecorator->getRates(); // Получает, кэширует и возвращает ставки от оригинального поставщика
```
или так:
```php
<?php 

use Chetkov\Money\Exchanger\RatesProvider\CacheDecorator\ExchangeRatesProviderCacheDecorator;
use Chetkov\Money\Exchanger\RatesProvider\CacheDecorator\Strategy\ClassPropertyCacheStrategy;
use Chetkov\Money\Exchanger\RatesProvider\CbrExchangeRatesProvider;

$ratesProvider = new CbrExchangeRatesProvider();
$classPropertyCacheStrategy = new ClassPropertyCacheStrategy();
$redisCacheStrategy = new RedisCache();

$redisCacheDecorator = new ExchangeRatesProviderCacheDecorator($ratesProvider, $redisCacheStrategy, 3600);
$classPropertyCacheDecorator = new ExchangeRatesProviderCacheDecorator($redisCacheDecorator, $classPropertyCacheStrategy, 60);

// 1) Смотрим в кэширующем свойстве класса 
// 2) Если пусто, смотрим в редис  
// 3) Если и там пусто, идем к оригинальному провайдеру
```

Аналогично обменникам Вы можете делать собственные реализации поставщиков. 


#### PS:
Пока на этом всё, но я думаю в скором времени пакет увидит еще множество доработок. По мере развития буду стараться поддерживать README в актуальном состоянии.

#### PPS:
Идея была взята из книги Мартина Фаулера: "Шаблоны корпоративных приложений". 