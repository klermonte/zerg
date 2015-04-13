zerg [![Build Status](https://travis-ci.org/klermonte/zerg.svg)](https://travis-ci.org/klermonte/zerg) [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/klermonte/zerg/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/klermonte/zerg/?branch=master) [![Code Coverage](https://scrutinizer-ci.com/g/klermonte/zerg/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/klermonte/zerg/?branch=master)
====

Zerg is a small PHP tool that allow you simply parse structured binary files like lsdj memory dump file, jpeg encoded image or your custom binary format file.

## Introdution
If you are reading this, chances are you know exactly why you need to read binary files in PHP. So I will not explain to you that this is not a good idea. Nevertheless, I like you needed to do this is in PHP. That's why I create zerg project. During creation, I was inspired by following projects: [alexras/bread](https://github.com/alexras/bread) and [themainframe/php-binary](https://github.com/themainframe/php-binary).

## Installation 
`composer require klermonte/zerg dev-master`  
Or add `"klermonte/zerg": "dev-master"` to your dependancy list in composer.json and run `composer update`

## Usage
```php
// Describe your binary format in zerg language
$fieldCollection = new \Zerg\Field\Collection([
    'stringValue' => ['string', 15],
    'intValue' => ['arr', 5, ['int', 8]],
    'enumValue' => ['enum', 8, [
            0 => 'zero',
            10 => 'ten',
            32 => 'many'
        ], ['default' => 'not found']
    ]
]);

// Wrap your data in one of zerg streams
$sourceStream = new \Zerg\Stream\StringStream("Hello from zerg123456");

//Get your data structure
$data  = $fieldCollection->parse($sourceStream);
print_r($data);
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
```

## Field types
### Integer
```php
// Object notation
// --------------------------------------
// $field = new Int(<size>, <options>);

$field = new Int(4);
$field = new Int('byte', [
    'signed' => true, 
    'formatter' => function($value) {
        return $value * 100;
    }
]);

// Array notation
// --------------------------------------
// $fieldArray = ['int', <size>, <options>];
```
Avaliable options  

Option name | Avaliable values | Description
------------|-------------|-------------
signed      | `boolean`, default `false` | Whether field value is signed or not
endian      | `PhpBio\Endian::ENDIAN_BIG` or<br>`PhpBio\Endian::ENDIAN_LITTLE` | Endianess of field
formatter   | `callable` | callback, that take 2 arguments:<br>`function ($parsedValue, $dataSetInstance) {...}`

### String
```php
// Object notation
// --------------------------------------
// $field = new String(<size>, <options>);

$field = new String(16);
$field = new String('short', [
    'endian' => PhpBio\Endian::ENDIAN_BIG, 
    'formatter' => function($value) {
        return str_repeat($value, 2);
    }
]);

// Array notation
// --------------------------------------
// $fieldArray = ['string', <size>, <options>];
```
Avaliable options  

Option name | Avaliable values | Description
------------|-------------|-------------
endian      | `PhpBio\Endian::ENDIAN_BIG` or<br>`PhpBio\Endian::ENDIAN_LITTLE` | Endianess of field
formatter   | `callable` | callback, that take 2 arguments:<br>`function ($parsedValue, DataSet $dataSet) {...}`

### Padding
```php
// Object notation
// --------------------------------------
// $field = new Padding(<size>);

$field = new Padding(16);

// Array notation
// --------------------------------------
// $fieldArray = ['padding', <size>];
```

### Enum
```php
// Object notation
// --------------------------------------
// $field = new Enum(<size>, <values>, <options>);

$field = new Enum(8, [0, 1, 2, 3]);
$field = new Enum('short', [
        1234 => 'qwerty1',
        2345 => 'qwerty2'
    ], [
        'default' => 'abcdef'
    ]
);

// Array notation
// --------------------------------------
// $fieldArray = ['enum', <values>, <options>];
```
Avaliable options  

Option name | Avaliable values | Description
------------|-------------|-------------
default     | `mixed`, optional | Value, that will be returned, if no one key from `values` matchs to parsed value

And all options from **Integer** field type.

### Conditional
```php
// Object notation
// --------------------------------------
// $field = new Conditional(<key>, <fields>, <options>);

$field = new Conditional('/path/to/key/value', [
        1 => ['int', 32],
        2 => ['string', 32]
    ], [
        'default' => ['padding', 32]
    ]
);

// Array notation
// --------------------------------------
// $fieldArray = ['conditional', <fields>, <options>];
```
Avaliable options  

Option name | Avaliable values | Description
------------|-------------|-------------
default     | `array`, optional | Field in array notation, that will be used, if no one key from `field` matchs to parsed value

### Array
```php
// Object notation
// --------------------------------------
// $field = new Arr(<count>, <field>, <options>);

$field = new Arr(10, ['int', 32]);

// Array notation
// --------------------------------------
// $fieldArray = ['arr', <field>, <options>];
```
Avaliable options  

Option name | Avaliable values | Description
------------|-------------|-------------
until       | `'eof'` or `callable` | If set, array field count parameter will be ignored, and field will parse values until End of File or callback return false, callback take one argument:<br>`function ($lastParsedValue) {...}`

### Collection
```php
// Object notation
// --------------------------------------
// $field = new Collection(<fields>, <options>);

$field = new Collection([
    'firstValue' => ['int', 32],
    'secondValue' => ['string', 32]
]);

// Array notation
// --------------------------------------
// $fieldArray = ['collection', <fields>, <options>];
// or just
// $fieldArray = <fields>;
```

### Back links
Size, count and conditional key parameters may be declared as a back link - path to already parsed value. Path can starts with `/` sign, that means root of data set or with '../' for relative path.
```php
$fieldCollection = new \Zerg\Field\Collection([
    'count' => ['string', 2],
    'intValue' => ['arr', '/count', ['int', 8]]
]);
$sourceStream = new \Zerg\Stream\StringStream("101234567890");
$data = $fieldCollection->parse($sourceStream);
print_r($data);
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
            0 => ['string', 80],
            10 => ['int', 16]
        ],
        [
            'default' => ['string', 2]
        ]
    ]
]);
$sourceStream = new \Zerg\Stream\StringStream("101234567890");
$data = $fieldCollection->parse($sourceStream);
print_r($data);
/*
Array
(
    [count] => 10
    [conditional] => 12849
)
*/
```
