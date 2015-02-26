<?php

namespace Zerg;

class DataSet implements \ArrayAccess, \Iterator
{
    /**
     * @var array Data wrapped by DataSet.
     * */
    private $data = [];

    /**
     * @var array Pointer of current insert/read position in internal format.
     *
     * The path is represented as an array of strings representing a route
     * through the levels of the DataSet to the required value.
     */
    private $currentPath = [];

    /**
     * @param array $data Data to be wrapped.
     */
    public function __construct(array $data = [])
    {
        $this->setData($data);
    }

    /**
     * Get wrapped data.
     *
     * @return array Currently wrapped data.
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Assign new data to DataSet
     * @param array $data Data to be wrapped by DataSet.
     */
    public function setData(array $data)
    {
        $this->data = (array) $data;
    }

    /**
     * Move into a level.
     *
     * @param string $level The level to move into.
     */
    public function push($level)
    {
        array_push($this->currentPath, $level);
    }

    /**
     * Move back out of the current level.
     */
    public function pop()
    {
        array_pop($this->currentPath);
    }

    /**
     * Set a value in the current level.
     *
     * @param string $name The name of the value to add.
     * @param string $value The value to add.
     */
    public function setValue($name, $value)
    {
        $child = & $this->data;

        foreach ($this->currentPath as $part) {
            if (isset($child[$part])) {
                $child = & $child[$part];
            } else {
                $child[$part] = [];
                $child = & $child[$part];
            }
        }

        $child[$name] = $value;
    }

    /**
     * Get a value by name from the current level.
     *
     * @param string $name The name of the value to retrieve.
     * @return mixed The found value. Returns null if the value cannot be found.
     */
    public function getValue($name)
    {
        $child = & $this->data;

        foreach ($this->currentPath as $part) {
            if (isset($child[$part])) {
                $child = & $child[$part];
            } else {
                return null;
            }
        }

        return isset($child[$name]) ? $child[$name] : null;
    }

    /**
     * Find a value by path within the DataSet instance.
     *
     * @see $currentPath
     * @param array $path Path in internal format.
     * @return mixed The found value. Returns null if the value cannot be found.
     */
    public function getValueByPath(array $path)
    {
        $child = $this->data;

        foreach ($path as $part) {
            if (isset($child[$part])) {
                $child = $child[$part];
            } else {
                return null;
            }
        }

        return $child;
    }

    /**
     * Assign a value by path within the DataSet instance.
     * Overwrites any existing value.
     *
     * @see $currentPath
     * @param array $path A path in internal format.
     * @param mixed $value The value to assign.
     */
    public function setValueByPath(array $path, $value)
    {
        $endPart = array_pop($path);
        $child = & $this->data;

        foreach ($path as $part) {
            if (isset($child[$part])) {
                $child = & $child[$part];
            } else {
                $child[$part] = [];
                $child = & $child[$part];
            }
        }

        $child[$endPart] = $value;
    }

    /**
     * Transform human path to internal DataSet format.
     *
     * @see $currentPath
     * @param string $path Path in human format ('/a/b' or 'a/../b')
     * @return array Path in internal format
     */
    public function parsePath($path)
    {
        return explode('/', trim($path, '/'));
    }

    /**
     * @inheritdoc
     * */
    public function current()
    {
        return current($this->data);
    }

    /**
     * @inheritdoc
     * */
    public function next()
    {
        next($this->data);
    }

    /**
     * @inheritdoc
     * */
    public function key()
    {
        return key($this->data);
    }

    /**
     * @inheritdoc
     * */
    public function valid()
    {
        return isset($this->data[$this->key()]);
    }

    /**
     * @inheritdoc
     * */
    public function rewind()
    {
        reset($this->data);
    }

    /**
     * @inheritdoc
     * */
    public function offsetExists($offset)
    {
        return isset($this->data[$offset]);
    }

    /**
     * @inheritdoc
     * */
    public function offsetGet($offset)
    {
        return $this->data[$offset];
    }

    /**
     * @inheritdoc
     * */
    public function offsetSet($offset, $value)
    {
        $this->data[$offset] = $value;
    }

    /**
     * @inheritdoc
     * */
    public function offsetUnset($offset)
    {
        unset($this->data[$offset]);
    }
}

