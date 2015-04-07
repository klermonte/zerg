<?php

namespace Zerg;

/**
 * DataSet is wrapper to array of data, that cat search and paste data by it's path.
 *
 * @since 0.1
 * @package Zerg
 */
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
     * Assign new data to DataSet.
     *
     * @param array $data Data to be wrapped by DataSet.
     */
    public function setData(array $data)
    {
        $this->data = $data;
    }

    /**
     * Move into a level.
     *
     * @param string|int $level The level to move into.
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
     * @param string|int $name The name of the value to add.
     * @param string|int|array|null $value The value to add.
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
     * @param string|int $name The name of the value to retrieve.
     * @return string|int|array|null The found value. Returns null if the value cannot be found.
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
     * @param string|array $path Path in internal or human format.
     * @return string|int|array|null The found value. Returns null if the value cannot be found.
     */
    public function getValueByPath($path)
    {
        if (is_string($path)) {
            $path = $this->parsePath($path);
        }

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
     * Assign a value by path within the DataSet instance,
     * overwrites any existing value.
     *
     * @see $currentPath
     * @param string|array $path A path in internal or human format.
     * @param string|int|array|null $value The value to assign.
     */
    public function setValueByPath($path, $value)
    {
        if (is_string($path)) {
            $path = $this->parsePath($path);
        }

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
     * @param string $path Path in human format ('/a/b' or 'a/../b' or './b/c').
     * @return array Path in internal format.
     * @throws Field\ConfigurationException If path could not be parsed.
     */
    public function parsePath($path)
    {
        $parts = explode('/', trim($path, '/'));

        $pathArray = [];
        if ($parts[0] == '.') {
            $pathArray = $this->currentPath;
            array_shift($parts);
        }

        foreach ($parts as $part) {
            if ($part == '..') {
                if (count($pathArray)) {
                    array_pop($pathArray);
                } else {
                    throw new Field\ConfigurationException("Invalid path. To many '..', can't move higher root.");
                }
            } else {
                $pathArray[] = $part;
            }
        }

        return $pathArray;
    }

    /**
     * Determines whether a given string is a DataSet path.
     *
     * @param mixed $value Tested string.
     * @return bool Whether tested string is a DataSet path.
     * @since 0.2
     */
    public static function isPath($value)
    {
        return is_string($value) && strpos($value, '/') !== false;
    }

    /**
     * Determines whether a given string is a DataSet absolute path.
     *
     * @param mixed $value Tested string.
     * @return bool Whether tested string is a DataSet absolute path.
     * @since 0.2
     */
    public static function isAbsolutePath($value)
    {
        return self::isPath($value) && strpos($value, '.') !== 0;
    }

    /**
     * Recursively find value by path.
     *
     * @param string $value Value path.
     * @return array|int|null|string
     * @since 1.0
     */
    public function resolvePath($value)
    {
        do {
            $value = $this->getValueByPath($value);
        } while (self::isPath($value));

        return $value;
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