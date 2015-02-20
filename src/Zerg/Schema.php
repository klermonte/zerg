<?php

namespace Zerg;

use Zerg\Field\Countable;
use Zerg\Stream\AbstractStream;

class Schema extends SchemaElement implements \ArrayAccess, \Iterator, Field\Countable
{
    use Field\CountableTrait;

    /**
     * @var DataSet
     */
    private $dataSet;

    /**
     * @var SchemaElement[]
     */
    private $elements = [];

    private $schemaArray = [];

    public function __construct($schemaArray = [], $properties = [])
    {
        $this->dataSet = new DataSet;
        $this->initFromArray($schemaArray);
        $this->configure($properties);
    }

    /**
     * @return array
     */
    public function getElements()
    {
        return $this->elements;
    }

    public function setElements($elements)
    {
        $this->elements = $elements;
    }

    /**
     * @return DataSet
     */
    public function getDataSet()
    {
        return $this->dataSet;
    }

    /**
     * @param $dataSet
     */
    public function setDataSet($dataSet)
    {
        $this->dataSet = $dataSet;
    }

    /**
     * @param array $array
     * @throws \Exception
     */
    private function initFromArray($array = [])
    {
        $this->schemaArray = $array;
        foreach ($array as $elementName => $elementParams) {

            if (!is_array($elementParams)) {
                throw new \Exception('Unknown element declaration');
            }

            $paramsKeys = array_keys($elementParams);

            if (array_keys($paramsKeys) !== $paramsKeys || is_array(reset($elementParams))) {
                $elementParams = ['schema', $elementParams];
            }

            $element = Field\Factory::get($elementParams);

            $element->setParent($this);
            $this->elements[$elementName] = $element;
        }
    }

    public function parse(AbstractStream $stream)
    {
        $currentDataSet = $this->dataSet;

        foreach ($this->elements as $elementName => $element) {

            if ($element instanceof self) {

                $childDataSet = $element->dataSet;
                $currentDataSet[$elementName] = $childDataSet;
                $element->clear();

                if ($element->getCount() > 1) {
                    for ($i = 0; $i < $element->getCount(); $i++) {
                        $childDataSet[$i] = $element->parse($stream);
                    }
                } else {
                    $element->parse($stream);
                }

            } else {
                $value = $element->parse($stream);
                if ($value !== null) {
                    $currentDataSet[$elementName] = $value;
                }
            }
        }

        return $currentDataSet;
    }

    public function write(AbstractStream $stream, $value)
    {
        // TODO: Implement write() method.
    }

    public function clear()
    {
        $dataSet = new DataSet;
        $dataSet->setParent($this->getParent()->getDataSet());
        $this->setDataSet($dataSet);

        foreach ($this->elements as $element) {
            if ($element instanceof self) {
                $element->clear();
            }
        }
    }

    /**
     * Whether a offset exists
     * @param mixed $offset An offset to check for
     * @return boolean true on success or false on failure
     */
    public function offsetExists($offset)
    {
        return isset($this->elements[$offset]);
    }

    /**
     * Return field instance by given offset
     * @param mixed $offset The offset to retrieve
     * @return SchemaElement
     */
    public function offsetGet($offset)
    {
        return $this->elements[$offset];
    }

    /**
     * add field or sub schema by given offset
     * @param mixed $offset The offset to assign the value to.
     * @param SchemaElement $value The value to set
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        $this->elements[$offset] = $value;
    }

    /**
     * Unset field by offset
     * @param mixed $offset The offset to unset
     * @return void
     */
    public function offsetUnset($offset)
    {
        unset($this->elements[$offset]);
    }

    /**
     * Return the current element
     * @return SchemaElement
     */
    public function current()
    {
        return current($this->elements);
    }

    /**
     * Move forward to next element
     * @return void Any returned value is ignored.
     */
    public function next()
    {
        next($this->elements);
    }

    /**
     * Return the key of the current element
     * @return mixed scalar on success, or null on failure.
     */
    public function key()
    {
        return key($this->elements);
    }

    /**
     * Checks if current position is valid
     * @return boolean The return value will be casted to boolean and then evaluated.
     * Returns true on success or false on failure.
     */
    public function valid()
    {
        return isset($this->elements[$this->key()]);
    }

    /**
     * Rewind the Iterator to the first element
     * @return void Any returned value is ignored.
     */
    public function rewind()
    {
        reset($this->elements);
    }
}