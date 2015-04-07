<?php

namespace Zerg\Field;

use Zerg\Stream\StringStream;

class PaddingTest extends \PHPUnit_Framework_TestCase
{
    public function sizes()
    {
        return [
            [4,4,4,0xD],
            [4,2,2,3],
            [8,8,8,0xf4],
            [4,12,8,0xf4],
            [8,12,4,0x4],
        ];
    }

    /**
     * @dataProvider sizes
     * */
    public function testParse($intSize, $padSize, $secondIntSeze, $result)
    {

        $stream = new StringStream("\xf3\xda\xf4\xdc\0");
        $int = new Int($intSize);
        $int2 = new Int($secondIntSeze);
        $padding = new Padding($padSize);

        $int->read($stream);
        $padding->parse($stream);

        $this->assertEquals($result, $int2->read($stream));
    }
}