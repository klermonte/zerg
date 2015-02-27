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
     * @var mixed Key of selected declaration.
     */
    protected $key = null;

    /**
     * @var array Array of possible declarations.
     */
    protected $fields = [];

    /**
     * @var array|null Default declaration.
     */
    protected $default = null;

    /**
     * Init field by key of needed declaration. Usually it is a DataSet path (back link)
     *
     * @param mixed $key DataSet path of field stored needed key.
     */
    public function init($key)
    {
        $this->key = $key;
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

        if (array_key_exists($key, $this->fields)) {
            $field = $this->fields[$key];
        } elseif ($this->default !== null) {
            $field = $this->default;
        } else {
            throw new InvalidKeyException(
                "Value '{$key}' does not correspond to a valid conditional key. Presented keys: '" .
                implode("', '", array_keys($this->fields)) . "'"
            );
        }

        $isAssoc = array_keys(array_keys($field)) !== array_keys($field);

        if ($isAssoc || is_array(reset($field))) {
            $field = ['collection', $field];
        }

        $field = Factory::get($field);
        $field->setDataSet($this->getDataSet());

        return $field;
    }
}