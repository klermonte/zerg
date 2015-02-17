<?php

namespace Zerg\Field;

use Zerg\Stream\AbstractStream;

class Padding extends AbstractField
{
    public function read(AbstractStream $stream)
    {
        $stream->skip($this->getLength());
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