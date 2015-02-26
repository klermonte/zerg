<?php

namespace Zerg\Stream;

use PhpBinaryReader\BinaryReader;

class StringStream extends AbstractStream
{
    /**
     * @param string $string
     */
    public function __construct($string)
    {
        $this->reader = new BinaryReader($string);
    }

} 