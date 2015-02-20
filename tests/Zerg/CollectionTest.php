<?php

namespace Zerg;

use Zerg\Field\Collection;
use Zerg\Stream\StringStream;

class CollectionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers Zerg\Field\Collection::offsetExists
     * @covers Zerg\Field\Collection::offsetGet
     * @covers Zerg\Field\Collection::offsetSet
     * @covers Zerg\Field\Collection::offsetUnset
     * */
    public function testArrayAccess()
    {
        $collection = new Collection([
            'a' => ['int', 8],
            'b' => ['string', 10]
        ]);

        $this->assertInstanceOf('\\Zerg\\Field\\Int', $collection['a']);
        $this->assertInstanceOf('\\Zerg\\Field\\String', $collection['b']);
        $this->assertFalse(isset($collection['c']));
        $collection['c'] = new Field\Int(1);
        $this->assertInstanceOf('\\Zerg\\Field\\Int', $collection['c']);
        unset($collection['a']);
        $this->assertFalse(isset($collection['a']));
    }

    /**
     * @covers Zerg\Field\Collection::current
     * @covers Zerg\Field\Collection::next
     * @covers Zerg\Field\Collection::key
     * @covers Zerg\Field\Collection::valid
     * @covers Zerg\Field\Collection::rewind
     * */
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

    /**
     * @covers Zerg\Field\Collection::initFromArray
     * */
    public function testInitFromArray()
    {
        $collection = new Collection([
            'a' => ['int', 8],
            'b' => ['string', 10],
            'c' => [
                'd' => ['int', 8, ['signed' => true]]
            ],
            'e' => ['string', 10, ['count' => 16]],
            'f' => ['collection', [
                'fa' => ['int', 8],
                'fc' => [
                    ['int', 8],
                    ['int', 8]
                ],
                'fb' => ['string', 10, ['utf' => true]]
            ], ['count' => 5]]
        ]);

        $this->assertInstanceOf('\\Zerg\\Field\\Int', $collection['a']);
        $this->assertInstanceOf('\\Zerg\\Field\\String', $collection['b']);
        $this->assertInstanceOf('\\Zerg\\Field\\Int', $collection['c']['d']);
        $this->assertInstanceOf('\\Zerg\\Field\\Collection', $collection['c']);
        $this->assertInstanceOf('\\Zerg\\Field\\Collection', $collection['c']->getParent());
        $this->assertSame($collection, $collection['c']->getParent());
        $this->assertEquals(16, $collection['e']->getCount());
        $this->assertInstanceOf('\\Zerg\\Field\\Collection', $collection['f']);
        $this->assertSame($collection['f']->getParent(), $collection['c']->getParent());
        $this->assertEquals(5, $collection['f']->getCount());
        $this->assertInstanceOf('\\Zerg\\Field\\Collection', $collection['f']['fc']);
        $this->assertSame($collection['f'], $collection['f']['fc']->getParent());
    }

    /**
     * @covers \Zerg\Field\Collection::parse
     * */
    public function testParse()
    {
        $collection = new Collection(
            [
                'a' => ['int', 4],
                ['padding', 4],
                'b' => ['string', 10],
                'c' => [
                    'd' => ['int', 8, ['signed' => true]],
                    'd2' => ['int', 8, ['signed' => true]],
                ],
                'e' => ['string', 10, ['count' => 16]],
                'f' => [
                    'collection', [
                        'fa' => ['string', 2, ['count' => 5]],
                        'fc' => [
                            'qwe1' => ['int', 8],
                            'qwe2' => ['int', 8],
                            'qwe3' => [
                                ['string', 2],
                                ['string', 2],
                            ]
                        ],
                        'fb' => ['string', 2, ['utf' => true]]
                    ], [
                        'count' => '/a',
                        'countCallback' => function ($count) {
                            return $count + 1;
                        }
                    ]
                ]
        ]);

        $stream = new StringStream('1adpiuhf3qurht3094h02r111111111ysahf890yasf9sdasdfasdfasdfafadfasfad
        adfasdf4h02r111111111ysahf890yasf9sdasdfasdfasdfafadfasfadadfasdf4h02r111111111ysahf
        890yasf9sdasdfasdfasdfafadfasfadadfasdf4h02r111111111ysahf890yasf9sdasdfasdfasdfafadfasfadadfasdf4h02r1111
        11111ysahf890yasf9sdasdfasdfasdfafadfasdfasdfafadfasfadadfasdf4h02r111111111ysahf
        890yasf9sdasdfasdfasdfafadfasfadadfasdf4h02r111111111ysahf890yasf9sdasdfasdfasdfafadfasfadadfasdf4h02r1111
        11111ysahf890yasf9sdasdfasdfasdfafadfasdfasdfafadfasfadadfasdf4h02r111111111ysahf
        890yasf9sdasdfasdfasdfafadfasfadadfasdf4h02r111111111ysahf890yasf9sdasdfasdfasdfafadfasfadadfasdf4h02r1111
        11111ysahf890yasf9sdasdfasdfasdfafadfasfadadfasdf4h02r111111111ysahf890yasf9sdasdfasdf
        asdfafadfasfadadfasdfgdbfgasda');

        $dataSet = $collection->parse($stream);

        $this->assertInternalType('int', $dataSet->getData()['a']);
        $this->assertInternalType('string', $dataSet->getData()['b']);
        $this->assertCount(16, $dataSet->getData()['e']);
        $this->assertCount(4, $dataSet->getData()['f']);
        $this->assertCount(5, $dataSet->getData()['f'][2]['fa']);
        $this->assertCount(3, $dataSet->getData()['f'][3]['fc']);
        $this->assertCount(2, $dataSet->getData()['f'][3]['fc']['qwe3']);
        $this->assertEquals(2, strlen($dataSet->getData()['f'][3]['fc']['qwe3'][1]));
    }
}