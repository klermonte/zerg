<?php

namespace Zerg\Field;

use Zerg\SchemaElement;
use Zerg\Stream\AbstractStream;

class Padding extends AbstractField implements Sizeable
{
    use SizeableTrait;

    public function __construct($size, $properties = [])
    {
        $this->setSize($size);
        $this->configure($properties);
    }

    public function parse(AbstractStream $stream)
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