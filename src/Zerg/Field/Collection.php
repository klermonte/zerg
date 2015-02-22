<?php

namespace Zerg\Field;

use Zerg\DataSet;
use Zerg\Stream\AbstractStream;

class Collection extends AbstractField implements \ArrayAccess, \Iterator
{
    /**
     * @var AbstractField[]
     */
    private $children = [];

    public function setMainParam($schemaArray)
    {
        $this->initFromArray($schemaArray);
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
        if (!$this->parent) {
            $this->dataSet = new DataSet;
        }

        $dataSet = $this->dataSet;
        foreach ($this->children as $fieldName => $fieldObj) {

            $fieldObj->setDataSet($dataSet);

            if ($fieldObj instanceof self) {

                $dataSet->push($fieldName);

                if ($fieldObj->getCount() > 1) {

                    for ($i = 0; $i < $fieldObj->getCount(); $i++) {
                        $dataSet->push($i);
                        $fieldObj->parse($stream);
                        $dataSet->pop();
                    }

                } else {

                    $fieldObj->parse($stream);
                }

                $dataSet->pop();

            } else {

                if ($fieldObj->getCount() > 1) {

                    $dataSet->push($fieldName);
                    for ($i = 0; $i < $fieldObj->getCount(); $i++) {
                        $dataSet->setValue($i, $fieldObj->parse($stream));
                    }
                    $dataSet->pop();

                } else {

                    $value = $fieldObj->parse($stream);
                    if ($value !== null) {
                        $dataSet->setValue($fieldName, $value);
                    }
                }
            }
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
        return isset($this->children[$offset]);
    }

    /**
     * Return field instance by given offset
     * @param mixed $offset The offset to retrieve
     * @return AbstractField
     */
    public function offsetGet($offset)
    {
        return $this->children[$offset];
    }

    /**
     * add field or sub schema by given offset
     * @param mixed $offset The offset to assign the value to.
     * @param AbstractField $value The value to set
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        $this->children[$offset] = $value;
    }

    /**
     * Unset field by offset
     * @param mixed $offset The offset to unset
     * @return void
     */
    public function offsetUnset($offset)
    {
        unset($this->children[$offset]);
    }

    /**
     * Return the current element
     * @return AbstractField
     */
    public function current()
    {
        return current($this->children);
    }

    /**
     * Move forward to next element
     * @return void Any returned value is ignored.
     */
    public function next()
    {
        next($this->children);
    }

    /**
     * Return the key of the current element
     * @return mixed scalar on success, or null on failure.
     */
    public function key()
    {
        return key($this->children);
    }

    /**
     * Checks if current position is valid
     * @return boolean The return value will be casted to boolean and then evaluated.
     * Returns true on success or false on failure.
     */
    public function valid()
    {
        return isset($this->children[$this->key()]);
    }

    /**
     * Rewind the Iterator to the first element
     * @return void Any returned value is ignored.
     */
    public function rewind()
    {
        reset($this->children);
    }
}