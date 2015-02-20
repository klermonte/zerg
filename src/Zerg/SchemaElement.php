<?php

namespace Zerg;

use Zerg\Stream\AbstractStream;

abstract class SchemaElement
{
    /**
     * @var Schema
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
     * @return Schema
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @param Schema $parent
     */
    public function setParent(Schema $parent)
    {
        $this->parent = $parent;
    }
}