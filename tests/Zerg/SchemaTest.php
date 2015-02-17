<?php

namespace Zerg;

class SchemaTest extends \PHPUnit_Framework_TestCase
{
    public function testSchemaCreation()
    {
        $schema = new Schema([
            'a' => ['int', 8],
            'b' => ['string', 10],
            'c' => [
                'd' => ['int', 8, ['signed' => true]]
            ],
            'e' => ['string', 10, ['multiply' => 16]]
        ]);

        $this->assertInstanceOf('\\Zerg\\Field\\Int', $schema['a']);
        $this->assertInstanceOf('\\Zerg\\Field\\String', $schema['b']);
        $this->assertInstanceOf('\\Zerg\\Field\\Int', $schema['c']['d']);
        $this->assertTrue($schema['c']['d']->isSigned());
        $this->assertFalse($schema['a']->isSigned());
        $this->assertInstanceOf('\\Zerg\\Field\\String', $schema['e'][15]);
    }
}