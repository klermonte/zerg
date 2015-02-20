<?php

namespace Zerg;

class DataSet implements \ArrayAccess, \Iterator
{
    private $data = [];

    /**
     * @var self
     * */
    private $parent = null;

    /**
     * @param array $data
     */
    public function __construct($data = [])
    {
        $this->data = (array) $data;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    public function setParent(self $dataSet)
    {
        $this->parent = $dataSet;
    }

    public function getParent()
    {
        return $this->parent;
    }

    public function getValueByPath($path)
    {
        $value = $this;
        if (strpos($path, '/') === 0) {
            $value = $this->getRoot();
        } elseif (strpos($path, './') === 0) {
            $path = substr($path, 2);
        }

        $pathParts = explode('/', trim($path, '/'));

        foreach ($pathParts as $pathPart) {

            if ($pathPart == '..') {
                if ($value instanceof self) {
                    $value = $value->parent;
                } else {
                    $value = null;
                }
            } elseif (isset($value[$pathPart])) {
                $value = $value[$pathPart];
            }

            if ($value === null) {
                print_r($this->getRoot());
                throw new \Exception('Wrong path');
            }
        }

        return $value;
    }

    public function getRoot()
    {
        $dataSet = $this;
        while ($dataSet->parent) {
            $dataSet = $dataSet->parent;
        }

        return $dataSet;
    }

    public function current()
    {
        return current($this->data);
    }

    public function next()
    {
        next($this->data);
    }

    public function key()
    {
        return key($this->data);
    }

    public function valid()
    {
        return isset($this->data[$this->key()]);
    }

    public function rewind()
    {
        reset($this->data);
    }

    public function offsetExists($offset)
    {
        return isset($this->data[$offset]);
    }

    public function offsetGet($offset)
    {
        return $this->data[$offset];
    }

    public function offsetSet($offset, $value)
    {
        $this->data[$offset] = $value;
    }

    public function offsetUnset($offset)
    {
        unset($this->data[$offset]);
    }
}