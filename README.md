#### Описание:
Пакет реализован для упрощения работы с деньгами. 

Предоставляет возможность:
- сложения, вычетания, умножения денежных значений;
- равномерного распределения денежного значения на N частей;
- пропорционального распределения денежного значения в соответствии с заданным соотношением;
- конвертации валют;
- сравнения деннежных значений между собой;
- выполнения всех выше перечисленных пунктов для денежных значений в разных валютах;

#### Установка:
`composer require v.chetkov/money`

#### Использование
```php
<?php

use Chetkov\Money\Money;

$money1 = new Money(100, 'RUB');
$money2 = new Money(200, 'RUB');
```

###### Add:
```php
$additionResult = $money1->add($money2);
echo $additionResult; 
// Result: 
// {
//     "amount":300,
//     "currency":"RUB",
//     "different_currency_behavior_strategy":"Chetkov\\Money\\Strategy\\ErrorWhenCurrenciesAreDifferentStrategy"
// }
```

###### Subtract:
```php
$subtractionResult = $money2->subtract($money1);
echo $subtractionResult; 
// Result: 
// {
//     "amount":100,
//     "currency":"RUB",
//     "different_currency_behavior_strategy":"Chetkov\\Money\\Strategy\\ErrorWhenCurrenciesAreDifferentStrategy"
// }
```

###### Multiply:
```php
$multiplicationResult = $money1->multiple(5);
echo $multiplicationResult; 
// Result: 
// {
//     "amount":500,
//     "currency":"RUB",
//     "different_currency_behavior_strategy":"Chetkov\\Money\\Strategy\\ErrorWhenCurrenciesAreDifferentStrategy"
// }
```

###### AllocateEvenly:
```php
$evenlyAllocationResult = $money1->allocateEvenly(4);
echo json_encode($evenlyAllocationResult);
// Result: 
// [
//     {
//         "amount":25,
//         "currency":"RUB",
//         "different_currency_behavior_strategy":"Chetkov\\Money\\Strategy\\ErrorWhenCurrenciesAreDifferentStrategy"
//     },
//     {
//         "amount":25,
//         "currency":"RUB",
//         "different_currency_behavior_strategy":"Chetkov\\Money\\Strategy\\ErrorWhenCurrenciesAreDifferentStrategy"
//     },
//     {
//         "amount":25,
//         "currency":"RUB",
//         "different_currency_behavior_strategy":"Chetkov\\Money\\Strategy\\ErrorWhenCurrenciesAreDifferentStrategy"
//     },
//     {
//         "amount":25,
//         "currency":"RUB",
//         "different_currency_behavior_strategy":"Chetkov\\Money\\Strategy\\ErrorWhenCurrenciesAreDifferentStrategy"
//     },
// ]

$evenlyAllocationResult = $money1->allocateEvenly(3, 4);
echo json_encode($evenlyAllocationResult);
// Result: 
// [
//     {
//         "amount":33.3333,
//         "currency":"RUB",
//         "different_currency_behavior_strategy":"Chetkov\\Money\\Strategy\\ErrorWhenCurrenciesAreDifferentStrategy"
//     },
//     {
//         "amount":33.3333,
//         "currency":"RUB",
//         "different_currency_behavior_strategy":"Chetkov\\Money\\Strategy\\ErrorWhenCurrenciesAreDifferentStrategy"
//     },
//     {
//         "amount":33.3334,
//         "currency":"RUB",
//         "different_currency_behavior_strategy":"Chetkov\\Money\\Strategy\\ErrorWhenCurrenciesAreDifferentStrategy"
//     },
// ]
```

###### AllocateProportionally:
```php
$proportionallyAllocationResult = $money1->allocateProportionally([0.18, 0.32, 0.5, 0.3, 1]);
echo json_encode($proportionallyAllocationResult);
// Result: 
// [
//     {
//         "amount":18,
//         "currency":"RUB",
//         "different_currency_behavior_strategy":"Chetkov\\Money\\Strategy\\ErrorWhenCurrenciesAreDifferentStrategy"
//     },
//     {
//         "amount":32,
//         "currency":"RUB",
//         "different_currency_behavior_strategy":"Chetkov\\Money\\Strategy\\ErrorWhenCurrenciesAreDifferentStrategy"
//     },
//     {
//         "amount":50,
//         "currency":"RUB",
//         "different_currency_behavior_strategy":"Chetkov\\Money\\Strategy\\ErrorWhenCurrenciesAreDifferentStrategy"
//     },
//     {
//         "amount":30,
//         "currency":"RUB",
//         "different_currency_behavior_strategy":"Chetkov\\Money\\Strategy\\ErrorWhenCurrenciesAreDifferentStrategy"
//     },
//     {
//         "amount":100,
//         "currency":"RUB",
//         "different_currency_behavior_strategy":"Chetkov\\Money\\Strategy\\ErrorWhenCurrenciesAreDifferentStrategy"
//     },
// ]
```

По умолчанию при попытке сложить, вычесть или сравнить значения в разных валютах будет выброшено исключение: _Chetkov\Money\Exception\OperationWithDifferentCurrenciesException_.

Если есть необходимость выполнять эти операции для значений в разных валютах, то при инстанциировании объекта _Money_ одним из аргументов конструктора нужно передать _SingleCurrencyConversionStrategy::class_. 
В следствии чего, перед выполнением выше перечисленных операций будет выполняться приведение второго значения к валюте первого. 

Для корректного конвертирования валют необходимо инстанциировать обменник (Exchanger - реализует шаблон Singleton) и загрузить в него курсы валютных пар.
Самый примитивный пример, реализовать это в bootstrap файле Вашего приложения. Или-же Вы можете сделать механизм автоматического обновления курсов валют с определенным интервалом (допустим загружая их с сайта ЦБ). Решение за Вами :)


###### exchange-rates.config.php
```php
return [
    'USD-RUB' => 66.34,
    'EUR-RUB' => 72.42,
    'JPY-RUB' => 0.61,
];
```

###### bootstrap.php
```php
<?php 

use Chetkov\Money\Exchanger;

// ...

$exchangeRates = require __ROOT__ . '/config/exchange-rates.config.php'
Exchanger::getInstance($exchangeRates);
```

```php
<?php

use Chetkov\Money\Money;
use Chetkov\Money\Strategy\SingleCurrencyConversionStrategy;

$moneyInUSD = new Money(100, 'USD', SingleCurrencyConversionStrategy::class);
$moneyInRUB = new Money(100, 'RUB', SingleCurrencyConversionStrategy::class);, 

echo $moneyInRUB->add($moneyInUSD);
// Result:
// {
//     "amount":6734,
//     "currency":"RUB",
//     "different_currency_behavior_strategy":"Chetkov\\Money\\Strategy\\ErrorWhenCurrenciesAreDifferentStrategy"
// }

echo $moneyInUSD->subtract($moneyInRUB);
// Result:
// {
//     "amount":98.49,
//     "currency":"USD",
//     "different_currency_behavior_strategy":"Chetkov\\Money\\Strategy\\ErrorWhenCurrenciesAreDifferentStrategy"
// }

echo $moneyInRUB->lessThan($moneyInUSD); // true

echo $moneyInUSD->moreThan($moneyInRUB); // true

echo $moneyInUSD->equals($moneyInRUB); // false
```

#### PS:
Пока на этом всё, но я думаю в скором времени пакет увидит еще множество доработок. По мере развития буду стараться поддерживать README в актуальном состоянии.

#### PPS:
Идея была взята из книги Мартина Фаулера: "Шаблоны корпоративных приложений". 