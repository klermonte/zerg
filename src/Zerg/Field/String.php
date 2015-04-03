<?php

namespace Zerg\Field;

use Zerg\Stream\AbstractStream;

/**
 * String represents string type data.
 *
 * Data, parsed by this type of field returns as it is in binary file.
 *
 * @since 0.1
 * @package Zerg\Field
 */
class String extends Scalar
{
    /**
     * Read string from stream as it is.
     *
     * @param AbstractStream $stream Stream from which read.
     * @return string Returned string.
     */
    public function read(AbstractStream $stream)
    {
        return $stream->getBuffer()->read($this->getSize());
    }
}