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
    private $children = [];

    /**
     * Init field by array of field declarations.
     *
     * @param array $schemaArray Array of declarations.
     */
    public function init($schemaArray)
    {
        $this->initFromArray($schemaArray);
    }

    /**
     * Add a new child node to children list.
     *
     * @param string $name The field name.
     * @param AbstractField $child Field instance.
     */
    public function addChild($name, AbstractField $child)
    {
        $child->setParent($this);
        $this->children[$name] = $child;
    }

    /**
     * Recursively call parse method of all children and store values in associated DataSet.
     *
     * @api
     * @param AbstractStream $stream Stream from which children read.
     * @return DataSet DataSet instance filled by children fields values.
     */
    public function parse(AbstractStream $stream)
    {
        if (!$this->parent) {
            $this->dataSet = new DataSet;
        }

        foreach ($this->children as $fieldName => $fieldObj) {

            $fieldObj->setDataSet($this->dataSet);
            $fieldObj->saveToDataSet($fieldName, $stream);

        }

        return $this->dataSet;
    }

    protected function saveToDataSetOnce($fieldName, AbstractStream $stream)
    {
        $this->dataSet->push($fieldName);
        $this->parse($stream);
        $this->dataSet->pop();
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

            if (!is_array($fieldParams)) {
                throw new ConfigurationException('Unknown element declaration');
            }

            $isAssoc = array_keys(array_keys($fieldParams)) !== array_keys($fieldParams);

            if ($isAssoc || is_array(reset($fieldParams))) {
                $fieldParams = ['collection', $fieldParams];
            }

            $this->addChild($fieldName, Factory::get($fieldParams));
        }
    }

    /**
     * @inheritdoc
     */
    public function offsetExists($offset)
    {
        return isset($this->children[$offset]);
    }

    /**
     * @inheritdoc
     */
    public function offsetGet($offset)
    {
        return $this->children[$offset];
    }

    /**
     * @inheritdoc
     */
    public function offsetSet($offset, $value)
    {
        $this->children[$offset] = $value;
    }

    /**
     * @inheritdoc
     */
    public function offsetUnset($offset)
    {
        unset($this->children[$offset]);
    }

    /**
     * @inheritdoc
     */
    public function current()
    {
        return current($this->children);
    }

    /**
     * @inheritdoc
     */
    public function next()
    {
        next($this->children);
    }

    /**
     * @inheritdoc
     */
    public function key()
    {
        return key($this->children);
    }

    /**
     * @inheritdoc
     */
    public function valid()
    {
        return isset($this->children[$this->key()]);
    }

    /**
     * @inheritdoc
     */
    public function rewind()
    {
        reset($this->children);
    }
}