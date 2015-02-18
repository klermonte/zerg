<?php

namespace Zerg\Schema;

use Zerg\DataSet;
use Zerg\Stream\AbstractStream;

class Schema extends SchemaElement implements \ArrayAccess, \Iterator, Countable
{
    use CountableTrait;

    /**
     * @var DataSet
     */
    protected $dataSet;

    /**
     * @var SchemaElement[]
     */
    private $elements = [];

    public function __construct($schemaArray = [], $properties = [])
    {
        $this->elements = $this->initFromArray($schemaArray);
        $this->dataSet = new DataSet;
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
     * @param array $array
     * @return array
     * @throws \Exception
     */
    private function initFromArray($array = [])
    {
        $elements = [];
        foreach ($array as $elementName => $elementParams) {

            if (!is_array($elementParams)) {
                throw new \Exception('Unknown element declaration');
            }

            $paramsKeys = array_keys($elementParams);

            if (array_keys($paramsKeys) !== $paramsKeys || is_array(reset($elementParams))) {
                $elementParams = ['schema', $elementParams];
            }

            $element = Factory::get($elementParams);

            $element->setParent($this);
            $elements[$elementName] = $element;
        }

        return $elements;
    }

    public function parse(AbstractStream $stream)
    {
        foreach ($this->elements as $elementName => $element) {
            $this->dataSet->setValue($elementName, $element->parse($stream));
        }
        return $this->dataSet;
    }

    public function write(AbstractStream $stream, $value)
    {
        // TODO: Implement write() method.
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