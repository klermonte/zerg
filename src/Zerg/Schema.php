<?php

namespace Zerg;

class Schema implements \ArrayAccess, \Iterator
{
    /**
     * @var array
     */
    private $fields = [];

    public function __construct($schemaArray = [])
    {
        $this->fields = $this->initFromArray($schemaArray);
    }

    /**
     * @return array
     */
    public function getFields()
    {
        return $this->fields;
    }

    private function setFields($fields)
    {
        $this->fields = $fields;
    }

    /**
     * @param array $array
     * @return array
     * @throws \Exception
     */
    private function initFromArray($array = [])
    {
        $fields = [];
        foreach ($array as $fieldName => $fieldParams) {

            if (!is_array($fieldParams)) {
                throw new \Exception('Unknown field declaration');
            }

            $paramsKeys = array_keys($fieldParams);
            $fieldType = reset($fieldParams);

            if (array_keys($paramsKeys) !== $paramsKeys || is_array($fieldType)) {

                // $paramsKeys is associative - recursively generate sub array
                $fields[$fieldName] = new self($fieldParams);

            } else {

                $field = Field\Factory::get($fieldParams);
                if (is_array($field)) {
                    $schema = new self;
                    $schema->setFields($field);
                    $fields[$fieldName] = $schema;
                } else {
                    $fields[$fieldName] = $field;
                }

                $schema = new self;
                $schema->fields = [];
            }
        }

        return $fields;
    }

    /**
     * Whether a offset exists
     * @param mixed $offset An offset to check for
     * @return boolean true on success or false on failure
     */
    public function offsetExists($offset)
    {
        return isset($this->fields[$offset]);
    }

    /**
     * Return field instance by given offset
     * @param mixed $offset The offset to retrieve
     * @return Field\AbstractField | Schema
     */
    public function offsetGet($offset)
    {
        return $this->fields[$offset];
    }

    /**
     * add field or sub schema by given offset
     * @param mixed $offset The offset to assign the value to.
     * @param Field\AbstractField | Schema $value The value to set
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        $this->fields[$offset] = $value;
    }

    /**
     * Unset field by offset
     * @param mixed $offset The offset to unset
     * @return void
     */
    public function offsetUnset($offset)
    {
        unset($this->fields[$offset]);
    }

    /**
     * Return the current element
     * @return Field\AbstractField | Schema
     */
    public function current()
    {
        return current($this->fields);
    }

    /**
     * Move forward to next element
     * @return void Any returned value is ignored.
     */
    public function next()
    {
        next($this->fields);
    }

    /**
     * Return the key of the current element
     * @return mixed scalar on success, or null on failure.
     */
    public function key()
    {
        return key($this->fields);
    }

    /**
     * Checks if current position is valid
     * @return boolean The return value will be casted to boolean and then evaluated.
     * Returns true on success or false on failure.
     */
    public function valid()
    {
        return isset($this->fields[$this->key()]);
    }

    /**
     * Rewind the Iterator to the first element
     * @return void Any returned value is ignored.
     */
    public function rewind()
    {
        reset($this->fields);
    }
}