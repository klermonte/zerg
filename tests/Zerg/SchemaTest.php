<?php

namespace Zerg;

use Zerg\Stream\StringStream;

class SchemaTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers Zerg\Schema::offsetExists
     * @covers Zerg\Schema::offsetGet
     * @covers Zerg\Schema::offsetSet
     * @covers Zerg\Schema::offsetUnset
     * */
    public function testArrayAccess()
    {
        $schema = new Schema([
            'a' => ['int', 8],
            'b' => ['string', 10]
        ]);

        $this->assertInstanceOf('\\Zerg\\Field\\Int', $schema['a']);
        $this->assertInstanceOf('\\Zerg\\Field\\String', $schema['b']);
        $this->assertFalse(isset($schema['c']));
        $schema['c'] = new Field\Int(1);
        $this->assertInstanceOf('\\Zerg\\Field\\Int', $schema['c']);
        unset($schema['a']);
        $this->assertFalse(isset($schema['a']));
    }

    /**
     * @covers Zerg\Schema::current
     * @covers Zerg\Schema::next
     * @covers Zerg\Schema::key
     * @covers Zerg\Schema::valid
     * @covers Zerg\Schema::rewind
     * */
    public function testIterator()
    {
        $types = [
            'a' => '\\Zerg\\Field\\Int',
            'b' => '\\Zerg\\Field\\String',
            'c' => '\\Zerg\\Schema'
        ];

        $schema = new Schema([
            'a' => ['int', 8],
            'b' => ['string', 10],
            'c' => [
                'a' => ['int', 8],
                'b' => ['string', 10],
            ]
        ]);

        $this->assertCount(3, $schema);

        foreach ($schema as $key => $field) {
            $this->assertInstanceOf($types[$key], $field);
        }

        $schema->rewind();
        while ($schema->valid()) {
            $this->assertInstanceOf($types[$schema->key()], $schema->current());
            $schema->next();
        }
    }

    /**
     * @covers Zerg\Schema::initFromArray
     * */
    public function testInitFromArray()
    {
        $schema = new Schema([
            'a' => ['int', 8],
            'b' => ['string', 10],
            'c' => [
                'd' => ['int', 8, ['signed' => true]]
            ],
            'e' => ['string', 10, ['count' => 16]],
            'f' => ['schema', [
                'fa' => ['int', 8],
                'fc' => [
                    ['int', 8],
                    ['int', 8]
                ],
                'fb' => ['string', 10, ['utf' => true]]
            ], ['count' => 5]]
        ]);

        $this->assertInstanceOf('\\Zerg\\Field\\Int', $schema['a']);
        $this->assertInstanceOf('\\Zerg\\Field\\String', $schema['b']);
        $this->assertInstanceOf('\\Zerg\\Field\\Int', $schema['c']['d']);
        $this->assertInstanceOf('\\Zerg\\Schema', $schema['c']);
        $this->assertInstanceOf('\\Zerg\\Schema', $schema['c']->getParent());
        $this->assertSame($schema, $schema['c']->getParent());
        $this->assertEquals(16, $schema['e']->getCount());
        $this->assertInstanceOf('\\Zerg\\Schema', $schema['f']);
        $this->assertSame($schema['f']->getParent(), $schema['c']->getParent());
        $this->assertEquals(5, $schema['f']->getCount());
        $this->assertInstanceOf('\\Zerg\\Schema', $schema['f']['fc']);
        $this->assertSame($schema['f'], $schema['f']['fc']->getParent());

        $this->assertInstanceOf('\\Zerg\\DataSet', $schema->getDataSet());
    }

    /**
     * @covers \Zerg\Schema::parse
     * */
    public function testParse()
    {
        $schema = new Schema([
            'a' => ['int', 4],
            ['padding', 4],
            'b' => ['string', 10],
            'c' => [
                'd' => ['int', 8, ['signed' => true]]
            ],
            'e' => ['string', 10, ['count' => 16]],
            'f' => [
                'schema', [
                    'fa' => ['string', 2, ['count' => 5]],
                    'fc' => [
                        ['int', 8],
                        ['int', 8],
                        ['string', '../../a']
                    ],
                    'fb' => ['string', 2, ['utf' => true]]
                ], [
                    'count' => './a',
                    'countCallback' => function($count) {
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

        $dataSet = $schema->parse($stream);


        $this->assertSame($dataSet['f'][0]->getParent(), $dataSet['f'][1]->getParent());
        $this->assertCount(4, $dataSet['f']);
        $this->assertCount(5, $dataSet['f'][2]['fa']);
        $this->assertEquals(3, strlen($dataSet['f'][3]['fc'][2]));
    }
}