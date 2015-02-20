<?php

namespace Zerg\Field;

use Zerg\Stream\AbstractStream;

class Collection extends AbstractField implements \ArrayAccess, \Iterator, Countable
{
    use CountableTrait;

    /**
     * @var AbstractField[]
     */
    private $children = [];

    public function __construct($schemaArray = [], $properties = [])
    {
        $this->initFromArray($schemaArray);
        $this->configure($properties);
    }

    /**
     * @param $name
     * @param AbstractField $child
     */
    public function setChild($name, AbstractField $child)
    {
        $child->setParent($this);
        $this->children[$name] = $child;
    }

    /**
     * @param array $fieldArray
     * @throws \Exception
     */
    private function initFromArray($fieldArray = [])
    {
        foreach ($fieldArray as $fieldName => $fieldParams) {

            if (!is_array($fieldParams)) {
                throw new \Exception('Unknown element declaration');
            }

            $isAssoc = array_keys(array_keys($fieldParams)) !== array_keys($fieldParams);

            if ($isAssoc || is_array(reset($fieldParams))) {
                $fieldParams = ['collection', $fieldParams];
            }

            $this->setChild($fieldName, Factory::get($fieldParams));
        }
    }

    public function parse(AbstractStream $stream)
    {
        $currentDataSet = $this->dataSet;

        foreach ($this->child as $elementName => $element) {

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

    /**
     * Whether a offset exists
     * @param mixed $offset An offset to check for
     * @return boolean true on success or false on failure
     */
    public function offsetExists($offset)
    {
        return isset($this->child[$offset]);
    }

    /**
     * Return field instance by given offset
     * @param mixed $offset The offset to retrieve
     * @return AbstractField
     */
    public function offsetGet($offset)
    {
        return $this->child[$offset];
    }

    /**
     * add field or sub schema by given offset
     * @param mixed $offset The offset to assign the value to.
     * @param AbstractField $value The value to set
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        $this->child[$offset] = $value;
    }

    /**
     * Unset field by offset
     * @param mixed $offset The offset to unset
     * @return void
     */
    public function offsetUnset($offset)
    {
        unset($this->child[$offset]);
    }

    /**
     * Return the current element
     * @return AbstractField
     */
    public function current()
    {
        return current($this->child);
    }

    /**
     * Move forward to next element
     * @return void Any returned value is ignored.
     */
    public function next()
    {
        next($this->child);
    }

    /**
     * Return the key of the current element
     * @return mixed scalar on success, or null on failure.
     */
    public function key()
    {
        return key($this->child);
    }

    /**
     * Checks if current position is valid
     * @return boolean The return value will be casted to boolean and then evaluated.
     * Returns true on success or false on failure.
     */
    public function valid()
    {
        return isset($this->child[$this->key()]);
    }

    /**
     * Rewind the Iterator to the first element
     * @return void Any returned value is ignored.
     */
    public function rewind()
    {
        reset($this->child);
    }
}