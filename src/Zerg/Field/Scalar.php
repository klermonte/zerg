<?php

namespace Zerg\Field;

use Zerg\Schema\Countable;
use Zerg\Schema\CountableTrait;
use Zerg\Schema\SchemaElement;
use Zerg\Schema\Sizeable;
use Zerg\Schema\SizeableTrait;
use Zerg\Stream\AbstractStream;

abstract class Scalar extends SchemaElement implements Countable, Sizeable
{
    use CountableTrait;
    use SizeableTrait;

    protected $valueCallback;

    abstract public function read(AbstractStream $stream);

    public function __construct($size, $properties = [])
    {
        $this->setSize($size);
        $this->configure($properties);
    }

    public function parse(AbstractStream $stream)
    {
        return $this->format($this->read($stream));
    }

    public function format($value)
    {
        if (is_callable($this->valueCallback)) {
            $value = call_user_func($this->valueCallback, $value);
        }
        return $value;
    }
}