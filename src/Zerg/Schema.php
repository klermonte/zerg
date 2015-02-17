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

            if (!$fieldType) {
                continue;
            }

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
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Whether a offset exists
     * @link http://php.net/manual/en/arrayaccess.offsetexists.php
     * @param mixed $offset <p>
     * An offset to check for.
     * </p>
     * @return boolean true on success or false on failure.
     * </p>
     * <p>
     * The return value will be casted to boolean if non-boolean was returned.
     */
    public function offsetExists($offset)
    {
        return isset($this->fields[$offset]);
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Offset to retrieve
     * @link http://php.net/manual/en/arrayaccess.offsetget.php
     * @param mixed $offset <p>
     * The offset to retrieve.
     * </p>
     * @return mixed Can return all value types.
     */
    public function offsetGet($offset)
    {
        return $this->fields[$offset];
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Offset to set
     * @link http://php.net/manual/en/arrayaccess.offsetset.php
     * @param mixed $offset <p>
     * The offset to assign the value to.
     * </p>
     * @param mixed $value <p>
     * The value to set.
     * </p>
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        $this->fields[$offset] = $value;
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Offset to unset
     * @link http://php.net/manual/en/arrayaccess.offsetunset.php
     * @param mixed $offset <p>
     * The offset to unset.
     * </p>
     * @return void
     */
    public function offsetUnset($offset)
    {
        unset($this->fields[$offset]);
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Return the current element
     * @link http://php.net/manual/en/iterator.current.php
     * @return mixed Can return any type.
     */
    public function current()
    {
        return current($this->fields);
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Move forward to next element
     * @link http://php.net/manual/en/iterator.next.php
     * @return void Any returned value is ignored.
     */
    public function next()
    {
        next($this->fields);
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Return the key of the current element
     * @link http://php.net/manual/en/iterator.key.php
     * @return mixed scalar on success, or null on failure.
     */
    public function key()
    {
        return key($this->fields);
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Checks if current position is valid
     * @link http://php.net/manual/en/iterator.valid.php
     * @return boolean The return value will be casted to boolean and then evaluated.
     * Returns true on success or false on failure.
     */
    public function valid()
    {
        return isset($this->fields[$this->key()]);
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Rewind the Iterator to the first element
     * @link http://php.net/manual/en/iterator.rewind.php
     * @return void Any returned value is ignored.
     */
    public function rewind()
    {
        reset($this->fields);
    }
}