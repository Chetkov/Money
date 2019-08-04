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
```
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
```
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
```
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
```
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

