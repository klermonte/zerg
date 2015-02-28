zerg [![Build Status](https://travis-ci.org/klermonte/zerg.svg)](https://travis-ci.org/klermonte/zerg) [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/klermonte/zerg/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/klermonte/zerg/?branch=master) [![Code Coverage](https://scrutinizer-ci.com/g/klermonte/zerg/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/klermonte/zerg/?branch=master)
====

Zerg is a small PHP tool that allow you simply parse structured binary files like lsdj memory dump file, jpeg encoded image or your custom binary format file.

## Introdusion
If you are reading this, chances are you know exactly why you need to read binary files in PHP. So I will not explain to you that this is not a good idea. Nevertheless, I like you needed to do this is in PHP. That's why I create zerg project. During creation, I was inspired by following projects: [alexras/bread](https://github.com/alexras/bread) and [themainframe/php-binary](https://github.com/themainframe/php-binary).

## Installation 
`composer require klermonte/zerg dev-master`  
Or add `"klermonte/zerg": "dev-master"` to your dependancy list in composer.json and run `composer update`

## Usage
```php
// Describe your binary format in zerg language
$fieldCollection = new \Zerg\Field\Collection([
    'stringValue' => ['string', 15],
     'intValue' => ['int', 8, [
        'count' => 5
    ]],
    'enumValue' => ['enum', 8, [
        'values' => [
            0 => 'zero',
            10 => 'ten',
            32 => 'many'
        ],
        'default' => 'not found'
    ]]
]);

// Wrap your data in one of zerg streams
$sourceStream = new \Zerg\Stream\StringStream("Hello from zerg123");

//Get your data structure
$dataSet = $fieldCollection->parse($sourceStream); // return \Zerg\DataSet instanse
print_r($dataSet->getData());
/*
Array
(
    [stringValue] => Hello from zerg
    [intValue] => Array
        (
            [0] => 49
            [1] => 50
            [2] => 51
            [3] => 52
            [4] => 53
        )

    [enumValue] => not found
)
*/

// or just one field
$intValue = $dataSet['intValue'];
```

## Field declaration
All zerg fields are described by array of 2 or 3 elements:

Element index | Function | Type | Value examples
------------- | -------- | ---- | ---------------
0 | Field type | string | `'int', 'string', 'enum', 'padding', 'conditional', 'collection'`
1 | Field init paramert | string or int | `'word'` or `32` for size, `'/header/packageSize'` for back link
3 | Field properties | array | `['signed' => true]`, `['values' => 1 => 'one', 10 => 'ten']`

## Field types
Type key | Init parameter | Description |Field properties
---------|----------------|-------------| ----
int      | Size in bits   | Inteder (signed or not) | **signed** (optional, bool, default false) - whether field value is signed or not
string   | Size in bytes  | String (utf of not) | **utf** (optional, bool, default false) - if set true, addition BOM checks will be processed
padding  | Size in bits   | Amount of bits that should be skipped and not saved | none
enum     | Size in bits   | Enumirable values | **values** (required, array) - array of values form which field choose return value by read key, **default** (optional, mixed, default null) - value that will be chosen if none of the values does not find
conditional | key | Field that transforms to other field depends by back link value | **fields** (required, array) - array of field declarations form which conditional choose needed by key parameter, **default** (optional, array, default null) - field declaration that will be chosen if none of the values does not find

Also, any type of field can be reapeated several times using  `'count'` field property. So you get array of values instead one value.

### Back links
Size, count and conditional key parameters may be declared as a back link - path to already parsed value. Path should starts with `/` sign, that means root of data set.
```php
$fieldCollection = new \Zerg\Field\Collection([
    'count' => ['string', 2],
    'intValue' => ['int', 8, [
        'count' => '/count'
    ]]
]);
$sourceStream = new \Zerg\Stream\StringStream("101234567890");
$dataSet = $fieldCollection->parse($sourceStream);
print_r($dataSet->getData());
/*
Array
(
    [count] => 10
    [intValue] => Array
        (
            [0] => 49
            [1] => 50
            [2] => 51
            [3] => 52
            [4] => 53
            [5] => 54
            [6] => 55
            [7] => 56
            [8] => 57
            [9] => 48
        )
)
*/
```
### Conditional example
```php
$fieldCollection = new \Zerg\Field\Collection([
    'count' => ['string', 2],
    'conditional' => ['conditional', '/count', [
        'fields' => [
            0 => ['string', 10],
            10 => ['int', 16]
        ],
        'default' => ['string', 2]
    ]]
]);
$sourceStream = new \Zerg\Stream\StringStream("101234567890");
$dataSet = $fieldCollection->parse($sourceStream);
print_r($dataSet->getData());
/*
Array
(
    [count] => 10
    [conditional] => 12849
)
*/
```
