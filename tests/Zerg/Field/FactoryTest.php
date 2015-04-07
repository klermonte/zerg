<?php

namespace Zerg\Field;

class FactoryTest extends \PHPUnit_Framework_TestCase
{
    public function types()
    {
        return [
            [['int', 1],                                '\\Zerg\\Field\Int'],
            [['int', 1, ['signed' => true]],            '\\Zerg\\Field\Int'],
            [['string', 1, ['assert' => 'qwe']],        '\\Zerg\\Field\String'],
            [['enum', 1, [], ['default' => 1]],         '\\Zerg\\Field\Enum'],
            [['conditional', 1, [], ['default' => []]], '\\Zerg\\Field\conditional'],
            [['padding', 1],                            '\\Zerg\\Field\padding'],
            [['collection', []],                        '\\Zerg\\Field\collection'],
            [['arr', 10, ['int', 1]],                   '\\Zerg\\Field\Arr'],
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
        Factory::get(['foo']);
    }
}