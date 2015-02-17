<?php

namespace Zerg\Field;

use Zerg\DataSet;
use Zerg\Stream\StringStream;

class ConditionalTest extends \PHPUnit_Framework_TestCase
{
    public function testRead()
    {
        $field = new Conditional('/c/d', [
            'schemas' => [
                1 => [
                    'wqe' => ['int', 8],
                    'asd' => ['string', 2]
                ],
                5 => [
                    'int' => ['int', 2]
                ],
            ],
            'default' => ['int', 8]
        ]);

        $field->setDataSet(new DataSet([
            'a' => 1,
            'b' => 5,
            'c' => [
                'd' => 3
            ]
        ]));

        $stream = new StringStream('123abcdefg');

        $schema = $field->read($stream);
        $this->assertInstanceOf('\\Zerg\\Field\\Int', $schema);

        $field->setPath('/a');
        $schema = $field->read($stream);
        $this->assertInstanceOf('\\Zerg\\Schema', $schema);
        $this->assertInstanceOf('\\Zerg\\Field\\Int', $schema->getFields()['wqe']);
        $this->assertInstanceOf('\\Zerg\\Field\\String', $schema->getFields()['asd']);

        $field->setPath('/b');
        $schema = $field->read($stream);
        $this->assertInstanceOf('\\Zerg\\Field\\Int', $schema->getFields()['int']);

    }
}