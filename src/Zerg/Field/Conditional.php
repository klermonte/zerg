<?php

namespace Zerg\Field;

use Zerg\Stream\AbstractStream;

/**
 * Conditional field can represent one of other given field or field collection depending on value of related field.
 *
 * @since 0.1
 * @package Zerg\Field
 */
class Conditional extends AbstractField
{
    /**
     * @var int|string Key of selected declaration.
     */
    protected $key;

    /**
     * @var array|AbstractField[] Array of possible declarations.
     */
    protected $fields;

    /**
     * @var array|null|AbstractField Default declaration.
     */
    protected $default;

    public function __construct($key, array $fields, $options = [])
    {
        $this->setKey($key);
        $this->setFields($fields);
        $this->configure($options);
    }

    /**
     * Resolve needed field instance and call it's parse method.
     *
     * @api
     * @param AbstractStream $stream Stream from which resolved field reads.
     * @return mixed Value returned by resolved field.
     */
    public function parse(AbstractStream $stream)
    {
        $field = $this;
        do {
            $field = $field->resolve();
        } while ($field instanceof self);

        return $field->parse($stream);
    }

    /**
     * Resolve value by DataSet path, choose related declaration and return field instance.
     *
     * @return AbstractField Field instance created by chosen declaration.
     * @throws InvalidKeyException If no one declaration is not related to resolved value and
     * default declaration is not presented.
     */
    private function resolve()
    {
        $key = $this->resolveProperty('key');

        if (isset($this->fields[$key])) {
            $field = $this->fields[$key];
        } elseif ($this->default !== null) {
            $field = $this->default;
        } else {
            throw new InvalidKeyException(
                "Value '{$key}' does not correspond to a valid conditional key. Presented keys: '" .
                implode("', '", array_keys($this->fields)) . "'"
            );
        }

        // get form cache
        if ($field instanceof AbstractField) {
            return $field;
        }

        $field = Factory::get($field);
        $field->setDataSet($this->getDataSet());

        // cache field instance
        if (isset($this->fields[$key])) {
            $this->fields[$key] = $field;
        } else {
            $this->default = $field;
        }

        return $field;
    }

    /**
     * @param int|string $key
     */
    public function setKey($key)
    {
        $this->key = $key;
    }

    /**
     * @param array|null|AbstractField $default
     */
    public function setDefault($default)
    {
        $this->default = $default;
    }

    /**
     * @param array|AbstractField[] $fields
     */
    public function setFields($fields)
    {
        $this->fields = $fields;
    }
}