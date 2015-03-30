<?php

namespace Zerg\Stream;

use PhpBio\BitBuffer;

/**
 * FileStream wraps string and allow fields to read data from it.
 *
 * @since 0.1
 * @package Zerg\Stream
 */
class StringStream extends AbstractStream
{
    /**
     * Return new string stream that will read data form given string.
     *
     * @param string $string
     */
    public function __construct($string)
    {
        $this->reader = new BitBuffer($string);
    }

}