<?php

namespace Zerg\Field;

use Zerg\DataSet;
use Zerg\Stream\AbstractStream;

/**
 * Class Collection compose other types of fields.
 *
 * This field return array of values, that are read from Stream by other types of fields.
 *
 * @since 0.1
 * @package Zerg\Field
 */
class Collection extends AbstractField implements \ArrayAccess, \Iterator
{
    /**
     * @var AbstractField[] List of children fields.
     */
    protected $fields;

    public function __construct($schema, $options = [])
    {
        $this->initFromArray($schema);
        $this->configure($options);
    }

    /**
     * Add a new child node to field list.
     *
     * @param string $name The field name.
     * @param AbstractField $child Field instance.
     */
    public function addField($name, AbstractField $child)
    {
        $this->fields[$name] = $child;
    }

    /**
     * Recursively call parse method of all children and store values in associated DataSet.
     *
     * @api
     * @param AbstractStream $stream Stream from which children read.
     * @return array Array of parsed values.
     */
    public function parse(AbstractStream $stream)
    {
        if (!($this->dataSet instanceof DataSet)) {
            $this->dataSet = new DataSet;
        }

        $this->rewind();
        do {
            $field = $this->current();
            $field->setDataSet($this->getDataSet());
            if ($field instanceof Conditional) {
                $field = $field->resolveField();
            }
            if ($field instanceof self) {
                $this->dataSet->push($this->key());
                $field->parse($stream);
                $this->dataSet->pop();
            } else {
                $this->dataSet->setValue($this->key(), $field->parse($stream));
            }
            $this->next();
        } while ($this->valid());

        if (isset($this->assert)) {
            $this->validate($this->dataSet->getValueByCurrentPath());
        }

        return $this->dataSet->getData();
    }

    /**
     * Recursively creates field instances form their declarations.
     *
     * @param array $fieldArray Array of declarations.
     * @throws ConfigurationException If one of declarations are invalid.
     */
    private function initFromArray(array $fieldArray = [])
    {
        foreach ($fieldArray as $fieldName => $fieldParams) {
            $this->addField($fieldName, Factory::get($fieldParams));
        }
    }

    /**
     * @inheritdoc
     */
    public function offsetExists($offset)
    {
        return isset($this->fields[$offset]);
    }

    /**
     * @inheritdoc
     */
    public function offsetGet($offset)
    {
        return $this->fields[$offset];
    }

    /**
     * @inheritdoc
     */
    public function offsetSet($offset, $value)
    {
        $this->fields[$offset] = $value;
    }

    /**
     * @inheritdoc
     */
    public function offsetUnset($offset)
    {
        unset($this->fields[$offset]);
    }

    /**
     * @inheritdoc
     * @return AbstractField
     */
    public function current()
    {
        return current($this->fields);
    }

    /**
     * @inheritdoc
     */
    public function next()
    {
        next($this->fields);
    }

    /**
     * @inheritdoc
     */
    public function key()
    {
        return key($this->fields);
    }

    /**
     * @inheritdoc
     */
    public function valid()
    {
        return isset($this->fields[$this->key()]);
    }

    /**
     * @inheritdoc
     */
    public function rewind()
    {
        reset($this->fields);
    }
}