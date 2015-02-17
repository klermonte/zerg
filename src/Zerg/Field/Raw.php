<?php

namespace Zerg\Field;

use Zerg\Stream\AbstractStream;

class Raw extends AbstractField
{
    public function read(AbstractStream $stream)
    {
        return $stream->read($this->getLength());
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