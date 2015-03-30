<?php

namespace Zerg\Stream;

use PhpBio\BitBuffer;

/**
 * FileStream wraps file and allow fields to read data from it.
 *
 * @since 0.1
 * @package Zerg\Stream
 */
class FileStream extends AbstractStream
{
    /**
     * Return new file stream that will read data form given file.
     *
     * @param string $path Path of file.
     */
    public function __construct($path)
    {
        $handle = fopen($path, 'rb');
        $this->reader = new BitBuffer($handle);
    }
}