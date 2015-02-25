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
}