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
    protected $propertyCache = [];

    /**
     * Read and process data from Stream.
     *
     * @api
     * @param AbstractStream $stream Stream from which field should read.
     * @return mixed Processed value.
     */
    abstract public function parse(AbstractStream $stream);

    /**
     * Associate given values to appropriate class properties.
     *
     * @param array $properties Associative array of properties values.
     */
    public function configure(array $properties)
    {
        foreach ($properties as $name => $value) {
            $methodName = 'set' . ucfirst(strtolower($name));
            if (method_exists($this, $methodName)) {
                $this->$methodName($value);
            }
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

    /**
     * Check that given value is valid.
     *
     * @param $value mixed Checked value.
     * @return true On success validation.
     * @throws AssertException On assertion fail.
     */
    public function validate($value)
    {
        $assert = $this->getAssert();
        if ($assert !== null) {
            if (is_callable($assert)) {
                if (!call_user_func($assert, $value, $this)) {
                    throw new AssertException(
                        sprintf('Custom validation fail with value (%s) "%s"', gettype($value), print_r($value, true))
                    );
                }
            } else {
                if ($value !== $assert) {
                    throw new AssertException(
                        sprintf(
                            'Failed asserting that actual value (%s) "%s" matches expected value (%s) "%s".',
                            gettype($value),
                            print_r($value, true),
                            gettype($assert),
                            print_r($assert, true)
                        )
                    );
                }
            }
        }

        return true;
    }

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
        $value = $this->$name;
        if (is_callable($value)) {
            return call_user_func($value, $this);
        }

        return $this->resolveValue($value);
    }

    /**
     * Find value in DataSet by given value if it is a path string.
     * Otherwise given value will be returned.
     *
     * @param $value
     * @return array|int|null|string
     * @since 0.2
     */
    private function resolveValue($value)
    {
        if (DataSet::isPath($value)) {
            if (empty($this->dataSet)) {
                throw new ConfigurationException('DataSet is required to resole value by path.');
            }
            $value = $this->dataSet->resolvePath($value);
        }

        if (is_array($value)) {
            foreach ($value as $key => $subValue) {
                $value[$key] = $this->resolveValue($subValue);
            }
        }

        return $value;
    }
}