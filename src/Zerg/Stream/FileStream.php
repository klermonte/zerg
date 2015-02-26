<?php

namespace Zerg\Stream;

use PhpBinaryReader\BinaryReader;

class FileStream extends AbstractStream
{
    /**
     * @param string $path
     */
    public function __construct($path)
    {
        $handle = fopen($path, 'rb');
        $this->reader = new BinaryReader($handle);
    }
}