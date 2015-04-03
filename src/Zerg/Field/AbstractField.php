<?php

namespace Zerg\Field;

use Zerg\DataSet;
use Zerg\Stream\AbstractStream;

/**
 * This abstract class represents any type of field - an entity that takes, processes and
 * returns some data from Stream. Also field has access
 * to DataSet from which it takes config values by given paths.
 *
 * @since 0.1
 * @package Zerg\Field
 */
abstract class AbstractField
{
    /**
     * @var DataSet The DataSet from which this field take values by path.
     */
    protected $dataSet;

    /**
     * @var mixed Value to compare parse result.
     */
    protected $assert;

    /**
     * @var array Cache to found values.
     */
    private $propertyCache = [];

    /**
     * Read and process data from Stream.
     *
     * @api
     * @param AbstractStream $stream Stream from which field should read.
     * @return mixed Processed value.
     */
    abstract public function parse(AbstractStream $stream);

    /**
     * Initialize field instance.
     *
     * Avery field has config values, which field itself sets by init() {@see init()}
     * method.
     *
     * @param array $properties Array of class properties to be set.
     */
    public function __construct(array $properties = [])
    {
        $this->init($properties);
    }

    /**
     * Implementation classes should override this method to init itself
     * by given properties array.
     *
     * @param array $properties Field properties array.
     * @return void
     */
    public function init(array $properties)
    {
        if (isset($properties['assert'])) {
            $this->setAssert($properties['assert']);
        }
    }

    /**
     * Return DataSet instance from which this field take back linked values.
     *
     * @return DataSet DataSet corresponding this field.
     */
    public function getDataSet()
    {
        return $this->dataSet;
    }

    /**
     * Set to field DataSet instance for back links.
     *
     * @param DataSet $dataSet DataSet ro correspond to this field.
     * @return self For chaining.
     */
    public function setDataSet(DataSet $dataSet)
    {
        $this->dataSet = $dataSet;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getAssert()
    {
        return $this->assert;
    }

    /**
     * @param mixed $assert
     * @return $this
     */
    public function setAssert($assert)
    {
        $this->assert = $assert;
        return $this;
    }

    /*    protected function saveToDataSet($fieldName, AbstractStream $stream)
        {
            $count = $this->getCount();

            if (empty($count) || $count == 1) {
                $this->saveToDataSetOnce($fieldName, $stream);
            } else {
                $this->saveToDataSetByCount($fieldName, $stream, $count);
            }
        }

        protected function saveToDataSetByCount($fieldName, AbstractStream $stream, $count)
        {
            $this->dataSet->push($fieldName);

            if (!is_array($count)) {
                $count = [$count];
            }

            $countPart = array_shift($count);

            for ($i = 0; $i < $countPart; $i++) {
                if (empty($count)) {
                    $this->saveToDataSetOnce($i, $stream);
                } else {
                    $this->saveToDataSetByCount($i, $stream, $count);
                }
            }

            $this->dataSet->pop();

        }

        protected function saveToDataSetOnce($fieldName, AbstractStream $stream)
        {
            $value = $this->parse($stream);
            if ($value !== null && !($value instanceof DataSet)) {
                $this->dataSet->setValue($fieldName, $value);
            }
        }*/

    /**
     * Process, set and return given property.
     *
     * Find given property in DataSet if it was set as path string and return it.
     * Otherwise already set value will be returned.
     *
     * @param string $name Property name.
     * @return int|string|array|null Found or already set property value.
     */
    protected function resolveProperty($name)
    {
        if (isset($this->propertyCache[$name])) {
            return $this->propertyCache[$name];
        }

        $value = $this->$name;
        if (is_callable($this->$name)) {
            $value = call_user_func($value, $this->dataSet);
        } else {
            $value = $this->resolveValue($value, $canBeCached);
            if ($canBeCached) {
                $this->propertyCache[$name] = $value;
            }
        }

        return $value;
    }

    /**
     * Find value in DataSet by given value if it is a path string.
     * Otherwise given value will be returned.
     *
     * @param $value
     * @param $canBeCached
     * @return array|int|null|string
     * @since 0.2
     */
    private function resolveValue($value, &$canBeCached = true)
    {
        if (DataSet::isPath($value) && !empty($this->dataSet)) {
            if (!DataSet::isAbsolutePath($value)) {
                $canBeCached = false;
            }
            $value = $this->dataSet->resolvePath($value);
        }

        if (is_array($value)) {
            foreach ($value as $key => $subValue) {
                $value[$key] = $this->resolveValue($subValue, $canBeCached);
            }
        }

        return $value;
    }
}