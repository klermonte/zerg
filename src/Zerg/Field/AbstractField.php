<?php

namespace Zerg\Field;

use Zerg\DataSet;
use Zerg\Stream\AbstractStream;

/**
 * This abstract class represents any type of field - an entity that takes, processes and
 * returns some data from Stream. Any field can be repeated given times. Also field has access
 * to DataSet from which it takes config values by given paths.
 *
 * @since 0.1
 * @package Zerg\Field
 */
abstract class AbstractField
{
    /**
     * @var Collection The collection field that holds this field.
     */
    protected $parent = null;

    /**
     * @var DataSet The DataSet from which this field take values by path.
     */
    protected $dataSet = null;

    /**
     * @var string|int Number of times that field should be repeated.
     */
    protected $count = 1;

    /**
     * @var callable Callback that changes field repeat count.
     */
    protected $countCallback;

    /**
     * Initialize field instance.
     *
     * Avery field has one main parameter, which it itself sets by init() {@see init()}
     * method. Others config values sets smarty by configure() {@see configure()} method.
     *
     * @param int|string|array $mainParam This parameter processed by class implementation.
     * @param array $properties Array of class properties to be set.
     */
    public function __construct($mainParam, $properties = [])
    {
        if (empty($properties)) {
            $properties = [];
        }
        $this->init($mainParam);
        $this->configure($properties);
    }

    /**
     * Implementation classes should override this method to init itself
     * by given main parameter.
     *
     * @param int|string|array $mainParam This parameter processed by class implementation.
     * @return void
     */
    abstract public function init($mainParam);

    /**
     * Read and process data from Stream.
     *
     * @api
     * @param AbstractStream $stream Stream from which field should read.
     * @return mixed Processed value.
     */
    abstract public function parse(AbstractStream $stream);

    /**
     * Init class properties by given values.
     *
     * @param array $properties Array of class properties to be set.
     * @return static For chaining.
     */
    public function configure(array $properties = [])
    {
        foreach ($properties as $name => $value) {
            $setter = 'set' . $name;
            if (method_exists($this, $setter)) {
                $this->$setter($value);
            } else {
                $this->$name = $value;
            }
        }
        return $this;
    }

    /**
     * Return parent collection.
     *
     * @return Collection Collection instance holding this field.
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Set parent collection.
     *
     * @param Collection $parent Collection instance, than will be hold this field.
     * @return static For chaining.
     */
    public function setParent(Collection $parent)
    {
        $this->parent = $parent;
        return $this;
    }

    /**
     * Return field repeat count.
     *
     * @return int Number of times that this field should be repeated.
     * @throws ConfigurationException If processed value is less zero.
     */
    public function getCount()
    {
        $this->resolveProperty('count');
        $count = (int) $this->getCallbackableProperty('count');

        if ($count < 0) {
            throw new ConfigurationException('Field count should not be less 0');
        }

        return $count;
    }

    /**
     * Set field repeat count.
     *
     * Size can be represented as a string containing on of size key words {@see $sizes}.
     * Also you can set path to already parsed value in DataSet.
     *
     * @param string|int $count
     * @return static For chaining.
     */
    public function setCount($count)
    {
        $this->count = $count;
        return $this;
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
     * Process, set and return given property.
     *
     * Find given property in DataSet if it was set as path string and return it.
     * Otherwise already set value will be returned.
     *
     * @param string $name Property name.
     * @return int|string|array|null Found or already set property value.
     * @throws ConfigurationException On invalid property value or
     * if there is not DataSet while property is set as path string.
     */
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

    /**
     * Process value by callback.
     *
     * Callback should be set by the property of the same name with 'Callback'
     * suffix on the end ('countCallback' for instance). If callback is not set
     * property value will be returned.
     *
     * @param string $name Property name.
     * @return mixed Processed or already set property value.
     */
    protected function getCallbackableProperty($name)
    {
        $callbackName = strtolower($name) . 'Callback';
        $value = $this->$name;
        if (is_callable($this->$callbackName)) {
            $value = call_user_func($this->$callbackName, $value);
        }
        return $value;
    }
}