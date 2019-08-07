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

use Chetkov\Money\Exchange\ExchangerInterface;
use Chetkov\Money\Exchange\SimpleExchanger;

$exchangeRates = [
    'USD-RUB' => 66.34,
    'EUR-RUB' => 72.42,
    'JPY-RUB' => 0.61,
];

return [
    'use_currency_conversation' => true,
    'exchanger_factory' => static function () use ($exchangeRates): ExchangerInterface {
        static $instance;
        if (null === $instance) {
            $instance = SimpleExchanger::getInstance($exchangeRates);
        }
        return $instance;
    },
];
```
Если параметер `'use_currency_conversation' => false`, то при выполнении операций с экземплярами _Money_ в разных валютах будет выброшено исключение: _Chetkov\Money\Exception\OperationWithDifferentCurrenciesException_.

Вы можете использовать существующие в пакете стратегии обмена валют: 
1) _SimpleExchangeStrategy_ - реализует шаблон Singleton.
Самый примитивный пример: Инстанциировать и выполнить загрузку курсов по валютным парам в bootstrap файле Вашего приложения. 
Вы можете сделать механизм получения курсов валют со стороннего ресурса (допустим с сайта ЦБ) или автоматического обновления с заданным интервалом. Решение за Вами :)

Или создать собственную стратегию обмена (должна реализовывать _ExchangeStrategyInterface_).

Далее необходимо загрузить описанный выше конфиг в _PackageConfig_, это необходимо для понимания:
1) включена-ли автоматическая конвертация валют
2) какой стратегией она будет выполняться
```php
<?php 

use Chetkov\Money\LibConfig;

$config = require __DIR__ . 'config/example.config.php';

LibConfig::getInstance($config);
```

#### Использование
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