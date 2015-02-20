<?php

namespace Zerg\Field;

use Zerg\DataSet;
use Zerg\SchemaElement;
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
        if ($this->getCount() > 1) {

            $arrayDataSet = new DataSet;
            $arrayDataSet->setParent($this->getParent()->getDataSet());

            for ($i = 0; $i < $this->getCount(); $i++) {
                $arrayDataSet[$i] = $this->format($this->read($stream));
            }

            $result = $arrayDataSet;

        } else {
            $result = $this->format($this->read($stream));
        }

        return $result;
    }

    public function format($value)
    {
        if (is_callable($this->valueCallback)) {
            $value = call_user_func($this->valueCallback, $value);
        }
        return $value;
    }
}