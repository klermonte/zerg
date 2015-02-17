<?php

namespace Zerg;

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
        $schema['c'] = new Field\Int;
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
            'e' => ['string', 10, ['count' => 16]]
        ]);

        $this->assertInstanceOf('\\Zerg\\Field\\Int', $schema['a']);
        $this->assertInstanceOf('\\Zerg\\Field\\String', $schema['b']);
        $this->assertInstanceOf('\\Zerg\\Field\\Int', $schema['c']['d']);
        $this->assertInstanceOf('\\Zerg\\Schema', $schema['c']);
        $this->assertTrue($schema['c']['d']->isSigned());
        $this->assertFalse($schema['a']->isSigned());
        $this->assertCount(16, $schema['e']);
        $this->assertInstanceOf('\\Zerg\\Field\\String', $schema['e'][15]);
    }
}