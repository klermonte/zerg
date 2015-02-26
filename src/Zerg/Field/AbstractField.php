<?php

namespace Zerg\Field;

use Zerg\DataSet;
use Zerg\Stream\AbstractStream;

abstract class AbstractField
{
    /**
     * @var Collection
     * */
    protected $parent = null;

    /**
     * @var DataSet
     */
    protected $dataSet = null;

    protected $count = 1;
    protected $countCallback;

    public function __construct($mainParam, $properties = [])
    {
        $this->init($mainParam);
        $this->configure($properties);
    }

    abstract public function init($mainParam);

    abstract public function parse(AbstractStream $stream);

    public function configure($properties = [])
    {
        foreach ((array) $properties as $name => $value) {
            $setter = 'set' . $name;
            if (method_exists($this, $setter)) {
                $this->$setter($value);
            } else {
                $this->$name = $value;
            }
        }
    }

    /**
     * @return Collection
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @param Collection $parent
     * @return self
     */
    public function setParent(Collection $parent)
    {
        $this->parent = $parent;
        return $this;
    }

    /**
     * @return DataSet
     */
    public function getDataSet()
    {
        return $this->dataSet;
    }

    /**
     * @param DataSet $dataSet
     * @return self
     */
    public function setDataSet(DataSet $dataSet)
    {
        $this->dataSet = $dataSet;
        return $this;
    }

    public function setCount($count)
    {
        $this->count = $count;
    }

    protected function resolveProperty($name)
    {
        $propertyValue = $this->$name;
        if (!is_numeric($propertyValue)) {
            if (strpos($propertyValue, '/') !== false) {
                if (($dataSet = $this->getDataSet()) instanceof DataSet) {
                    while (strpos($propertyValue, '/') !== false) {
                        $propertyValue = $dataSet->getValueByPath($dataSet->parsePath($this->$name));
                        $this->$name = $propertyValue;
                    }
                } else {
                    throw new ConfigurationException('DataSet required to get value by path');
                }
            } else {
                throw new ConfigurationException("'{$propertyValue}' is not valid {$name} value");
            }
        }


        return $propertyValue;
    }

    protected function getCallbackableProperty($name)
    {
        $callbackName = strtolower($name) . 'Callback';
        $value = $this->$name;
        if (is_callable($this->$callbackName)) {
            $value = call_user_func($this->$callbackName, $value);
        }
        return $value;
    }

    public function getCount()
    {
        $this->resolveProperty('count');
        $count = (int) $this->getCallbackableProperty('count');

        if ($count < 0) {
            throw new ConfigurationException('Field count should not be less 0');
        }

        return $count;
    }
}