<?php

namespace Zerg\Schema;

use Zerg\Stream\AbstractStream;

abstract class SchemaElement
{
    protected $parent;

    abstract public function __construct($mainParam, $properties = []);

    abstract public function parse(AbstractStream $stream);
    abstract public function write(AbstractStream $stream, $value);

    public function configure($properties = [])
    {
        foreach ((array) $properties as $name => $value) {
            $this->$name = $value;
        }
    }

    /**
     * @return mixed
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @param mixed $parent
     */
    public function setParent($parent)
    {
        $this->parent = $parent;
    }
}