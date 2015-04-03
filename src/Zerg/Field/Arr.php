<?php

namespace Zerg\Field;


use Zerg\Stream\AbstractStream;

class Arr extends AbstractField
{
    /**
     * @var int Count of elements.
     */
    protected $count;

    /**
     * @var string|callable
     */
    protected $until;

    /**
     * @var AbstractField Field to be repeated.
     */
    protected $field;

    /**
     * Init array field by required properties.
     *
     * @param array $properties Field properties array.
     * @return void
     */
    public function init($properties)
    {
        parent::init($properties);

        if (isset($properties['field'])) {
            $this->setField($properties['field']);
        } else {
            throw new ConfigurationException('Array field must be configured by field property');
        }

        if (isset($properties['until'])) {
            $this->setUntil($properties['until']);
        } elseif (isset($properties['count'])) {
            $this->setCount($properties['count']);
        } else {
            throw new ConfigurationException('Array field must be configured by either count or until property');
        }
    }

    /**
     * Read and process data from Stream.
     *
     * @api
     * @param AbstractStream $stream Stream from which field should read.
     * @return array Array of values.
     */
    public function parse(AbstractStream $stream)
    {
        $value = [];
        $field = $this->getField();
        for ($i = 0; $i < $this->getCount(); $i ++) {
            $value[$i] = $field->parse($stream);
        }
        return $value;
    }

    /**
     * @return int
     */
    public function getCount()
    {
        return (int) $this->resolveProperty('count');
    }

    /**
     * @param int $count
     * @return $this
     */
    public function setCount($count)
    {
        $this->count = $count;
        return $this;
    }

    /**
     * @return callable|string
     */
    public function getUntil()
    {
        return $this->until;
    }

    /**
     * @param callable|string $until
     * @return $this
     */
    public function setUntil($until)
    {
        $this->until = $until;
        return $this;
    }

    /**
     * @return AbstractField
     */
    public function getField()
    {
        $field = $this->resolveProperty('field');
        if (is_array($field)) {
            $field = Factory::get($field);
            $field->setDataSet($this->dataSet);
        }
        return $field;
    }

    /**
     * @param AbstractField $field
     * @return $this
     */
    public function setField($field)
    {
        $this->field = $field;
        return $this;
    }
}