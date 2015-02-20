<?php

namespace Zerg\Field;

use Zerg\DataSet;
use Zerg\Stream\AbstractStream;

abstract class AbstractField
{
    /**
     * @var Collection
     * */
    protected $parent = null;

    /**
     * @var DataSet
     */
    protected $dataSet = null;

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
     * @return self
     */
    public function setParent(Collection $parent)
    {
        $this->parent = $parent;
        return $this;
    }

    /**
     * @return DataSet
     */
    public function getDataSet()
    {
        return $this->dataSet;
    }

    /**
     * @param DataSet $dataSet
     * @return self
     */
    public function setDataSet(DataSet $dataSet)
    {
        $this->dataSet = $dataSet;
        return $this;
    }


}