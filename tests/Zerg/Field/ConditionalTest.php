<?php

namespace Zerg\Field;

use Zerg\DataSet;
use Zerg\Stream\StringStream;

class ConditionalTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Conditional
     */
    public $field;
    public $stream;

    public function setUp()
    {
        $this->field = new Conditional('/c/d', [
                1 => [
                    'wqe' => ['int', 8],
                    'asd' => ['string', 16]
                ],
                5 => ['int', 8],
                10 => ['conditional', '/a', [
                    1 => ['string', 48]
                ]]
            ],
            ['default' => ['int', 8]]
        );

        $this->field->setDataSet(new DataSet([
            'a' => 1,
            'b' => 5,
            'c' => [
                'd' => 3,
                'e' => 10,
                'f' => '/c/d'
            ]
        ]));

        $this->stream = new StringStream('123abcdefghiklm');
    }

    public function testParse()
    {
        $field = $this->field;
        $stream = $this->stream;

        $value = $field->parse($stream);
        $this->assertSame(49, $value);

        $field->setKey('/a');
        $value = $field->parse($stream);
        $this->assertSame(50, $value['wqe']);
        $this->assertSame('3a', $value['asd']);

        $field->setKey('/b');
        $value = $field->parse($stream);
        $this->assertSame(ord('b'), $value);

        $field->setKey('/c/e');
        $value = $field->parse($stream);
        $this->assertSame('cdefgh', $value);

        $field->setKey('/c/f');
        $value = $field->parse($stream);
        $this->assertSame(ord('i'), $value);
    }

    /**
     * @expectedException \Zerg\Field\InvalidKeyException
     * */
    public function testKeyException()
    {
        $this->field->setKey('/a/y');
        $this->field->setDefault(null);
        $this->field->parse($this->stream);
    }

    public function testMassConfig()
    {
        $conditional1 = new Conditional('/some/path', [['int', 8], ['int', 8]], ['assert' => 10, 'default' => ['int', 8]]);
        $conditional2 = new Conditional([
            'key' => '/some/path',
            'fields' => [['int', 8], ['int', 8]],
            'assert' => 10,
            'default' => ['int', 8],
            'signed' => true
        ]);
        $this->assertEquals($conditional1, $conditional2);
    }
}