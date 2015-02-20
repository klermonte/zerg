<?php

namespace Zerg\Field;

use Zerg\Stream\AbstractStream;

abstract class AbstractField
{
    /**
     * @var Collection
     * */
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
     * @return Collection
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @param Collection $parent
     */
    public function setParent(Collection $parent)
    {
        $this->parent = $parent;
    }
}