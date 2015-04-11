<?php

namespace Zerg\Field;

use Zerg\Stream\StringStream;

class CollectionTest extends \PHPUnit_Framework_TestCase
{
    public function testArrayAccess()
    {
        $collection = new Collection([
            'a' => ['int', 8],
            'b' => ['string', 10]
        ]);

        $this->assertInstanceOf('\\Zerg\\Field\\Int', $collection['a']);
        $this->assertInstanceOf('\\Zerg\\Field\\String', $collection['b']);
        $this->assertFalse(isset($collection['c']));
        $collection['c'] = new Int(1);
        $this->assertInstanceOf('\\Zerg\\Field\\Int', $collection['c']);
        unset($collection['a']);
        $this->assertFalse(isset($collection['a']));
    }

    public function testIterator()
    {
        $types = [
            'a' => '\\Zerg\\Field\\Int',
            'b' => '\\Zerg\\Field\\String',
            'c' => '\\Zerg\\Field\\Collection'
        ];

        $collection = new Collection([
            'a' => ['int', 8],
            'b' => ['string', 10],
            'c' => [
                'a' => ['int', 8],
                'b' => ['string', 10],
            ]
        ]);

        $this->assertCount(3, $collection);

        foreach ($collection as $key => $field) {
            $this->assertInstanceOf($types[$key], $field);
        }

        $collection->rewind();
        while ($collection->valid()) {
            $this->assertInstanceOf($types[$collection->key()], $collection->current());
            $collection->next();
        }
    }

    public function testInitFromArray()
    {
        $collection = new Collection([
            'a' => ['int', 8],
            'b' => ['string', 10],
            'c' => [
                'd' => ['int', 8, ['signed' => true]]
            ],
            'e' => ['arr', 16, ['string', 10]],
            'f' => ['arr', 5, [
                'collection', [
                    'fa' => ['int', 8],
                    'fc' => [
                        ['int', 8],
                        ['int', 8]
                    ],
                    'fb' => ['string', 10, ['assert' => 'qweqweqweq']]
                ]
            ]]
        ]);

        $this->assertInstanceOf('\\Zerg\\Field\\Int', $collection['a']);
        $this->assertInstanceOf('\\Zerg\\Field\\String', $collection['b']);
        $this->assertInstanceOf('\\Zerg\\Field\\Int', $collection['c']['d']);
        $this->assertInstanceOf('\\Zerg\\Field\\Collection', $collection['c']);
        $this->assertEquals(16, $collection['e']->getCount());
        $this->assertInstanceOf('\\Zerg\\Field\\Arr', $collection['f']);
        $this->assertEquals(5, $collection['f']->getCount());
    }

    public function testParse()
    {
        $collection = new Collection([
            'a' => ['int', 'byte', ['assert' => 49]],
            'b' => ['conditional', '/a',
                [
                    0  => ['int', 8],
                    49 => ['conditional', '/a',
                        [
                            49 => ['string', 6]
                        ]
                    ]
                ],
                [
                    'default' => [
                        ['int', 8],
                        ['int', 8]
                    ]
                ]
            ],
            'c' => [
                'collection',
                [
                    'd'  => ['int', 8, ['signed' => true]],
                    'd2' => ['int', 8, ['signed' => true]],
                ],
                [
                    'assert' => [
                        'd' => 76,
                        'd2' => 76
                    ]
                ]
            ],
            'e' => ['arr', 16, ['string', 80]],
            'f' => [
                'arr',
                function (Arr $arrayField) {
                    $value = $arrayField
                        ->getDataSet()
                        ->getValueByPath('/a') - 45;
                    return $value;
                },
                [
                    'fa' => ['arr', 5, ['field' => ['string', 16]]],
                    'fc' => [
                        'qwe1' => ['int', 8],
                        'qwe2' => ['int', 8],
                        'qwe3' => [
                            ['string', 16],
                            ['string', 16],
                        ]
                    ],
                    'fb' => ['string', 16, ['assert' => function ($readString) {
                        return $readString === 'LL';
                    }]]
                ]
            ],
        ]);

        $stream = new StringStream(str_pad('', 1000, '1'));

        $dataSet = $collection->parse($stream);

        $this->assertInternalType('int', $dataSet['a']);
        $this->assertInternalType('string', $dataSet['b']);
        $this->assertCount(16, $dataSet['e']);
        $this->assertCount(4, $dataSet['f']);
        $this->assertCount(5, $dataSet['f'][2]['fa']);
        $this->assertCount(3, $dataSet['f'][3]['fc']);
        $this->assertCount(2, $dataSet['f'][3]['fc']['qwe3']);
        $this->assertEquals(2, strlen($dataSet['f'][3]['fc']['qwe3'][1]));
    }

    /**
     * @expectedException \Zerg\Field\ConfigurationException
     * */
    public function testCreationException()
    {
        new Collection(['foo']);
    }

    /**
     * @expectedException \PHPUnit_Framework_Error
     */
    public function testCreationError()
    {
        new Collection('foo');
    }
}