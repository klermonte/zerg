<?php

namespace Zerg\Field;

class FactoryTest extends \PHPUnit_Framework_TestCase
{
    public function types()
    {
        return [
            [['int', 1],         '\\Zerg\\Field\Int'],
            [['string', 1],      '\\Zerg\\Field\String'],
            [['enum', 1],        '\\Zerg\\Field\Enum'],
            [['conditional', 1], '\\Zerg\\Field\conditional'],
            [['padding', 1],     '\\Zerg\\Field\padding'],
            [['collection', []], '\\Zerg\\Field\collection'],
        ];
    }

    /**
     * @dataProvider types
     * */
    public function testCreation($type, $class)
    {
        $field = Factory::get($type);
        $this->assertInstanceOf($class, $field);
    }

    /**
     * @expectedException \Zerg\Field\ConfigurationException
     * */
    public function testCreationException()
    {
        $field = Factory::get(['foo']);
    }
}