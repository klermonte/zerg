<?php

namespace Zerg\Field;

use Zerg\DataSet;
use Zerg\Stream\StringStream;

class ConditionalTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers \Zerg\Field\Conditional::setDataSet
     * @covers \Zerg\Field\Conditional::configure
     * @covers \Zerg\Field\Conditional::parse
     */
    public function testParse()
    {
        $field = new Conditional('/c/d', [
            'fields' => [
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

        $returnField = $field->parse($stream);
        $this->assertInstanceOf('\\Zerg\\Field\\Int', $returnField);

        $field->configure(['path' => '/a']);
        $returnField = $field->parse($stream);
        $this->assertInstanceOf('\\Zerg\\Field\\Collection', $returnField);
        $this->assertInstanceOf('\\Zerg\\Field\\Int', $returnField['wqe']);
        $this->assertInstanceOf('\\Zerg\\Field\\String', $returnField['asd']);

        $field->configure(['path' => '/b']);
        $returnField = $field->parse($stream);
        $this->assertInstanceOf('\\Zerg\\Field\\Int', $returnField['int']);

    }
}