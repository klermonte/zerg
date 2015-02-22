<?php

namespace Zerg\Field;

use Zerg\Stream\AbstractStream;

class Padding extends Scalar
{
    public function read(AbstractStream $stream)
    {
        $stream->skip($this->getSize());
        return null;
    }

    /**
     * @param AbstractStream $stream
     * @param mixed $value
     * @return bool
     */
    public function write(AbstractStream $stream, $value)
    {
        // TODO: Implement write() method.
    }
}