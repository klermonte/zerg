<?php

namespace Zerg\Field;

use Zerg\DataSet;
use Zerg\Stream\StringStream;

class ConditionalTest extends \PHPUnit_Framework_TestCase
{
    public function testParse()
    {
        $field = new Conditional('/c/d', [
            'fields' => [
                1 => [
                    'wqe' => ['int', 8],
                    'asd' => ['string', 2]
                ],
                5 => ['int', 8],
                10 => ['conditional', '/a', [
                    'fields' => [
                        1 => ['string', 6]
                    ]
                ]]
            ],
            'default' => ['int', 8]
        ]);

        $field->setDataSet(new DataSet([
            'a' => 1,
            'b' => 5,
            'c' => [
                'd' => 3,
                'e' => 10,
                'f' => '/c/d'
            ]
        ]));

        $stream = new StringStream('123abcdefghiklm');

        $value = $field->parse($stream);
        $this->assertSame(49, $value);

        $field->configure(['key' => '/a']);
        $value = $field->parse($stream);
        $this->assertInstanceOf('\\Zerg\\DataSet', $value);
        $this->assertSame(50, $value['wqe']);
        $this->assertSame('3a', $value['asd']);

        $field->configure(['key' => '/b']);
        $value = $field->parse($stream);
        $this->assertSame(ord('b'), $value);

        $field->configure(['key' => '/c/e']);
        $value = $field->parse($stream);
        $this->assertSame('cdefgh', $value);

        $field->configure(['key' => '/c/f']);
        $value = $field->parse($stream);
        $this->assertSame(ord('i'), $value);
    }
}