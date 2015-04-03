<?php

namespace Zerg\Field;

use Zerg\Stream\StringStream;

class ScalarTest extends \PHPUnit_Framework_TestCase
{
    public function testCreation()
    {
        $callback = function($value) {
            return $value - 2 * 8;
        };

        $field = new Int('byte', [
            'signed' => true,
            'formatter' => $callback
        ]);

        $this->assertSame(8, $field->getSize());
        $this->assertSame($callback, $field->getFormatter());
        $this->assertTrue($field->getSigned());
    }

    public function testValueCallback()
    {
        $stream = new StringStream('123abcdefgqwertyahnytjssdadfkjhb');
        $field = new Int('byte', [
            'formatter' => function($value) {
                return $value - 2 * 8;
            }
        ]);

        $this->assertEquals(33, $field->parse($stream));
    }
}