<?php

namespace Zerg\Field;

use Zerg\Stream\AbstractStream;

/**
 * Enum field return one of given values depends on read value.
 *
 * @since 0.1
 * @package Zerg\Field
 */
class Enum extends Int
{
    /**
     * @var array Array of possible values. Keys should by integer.
     */
    protected $values;

    /**
     * @var mixed Default value, if no one of possible values are not relate to read value.
     */
    protected $default;

    public function __construct($size, array $values = [], $options = [])
    {
        parent::__construct($size, $options);
        if (!empty($values)) {
            $this->setValues($values);
        }
    }

    /**
     * Read key from Stream, and return value by this key or default value.
     *
     * @param AbstractStream $stream Stream from which resolved field reads.
     * @return object|integer|double|string|array|boolean|callable Value by read key or default value if present.
     * @throws InvalidKeyException If read key is not exist and default value is not presented.
     */
    public function read(AbstractStream $stream)
    {
        $key = parent::read($stream);
        $values = $this->getValues();

        if (array_key_exists($key, $values)) {
            $value = $values[$key];
        } else {
            $value = $this->getDefault();
        }

        if ($value === null) {
            throw new InvalidKeyException(
                "Value '{$key}' does not correspond to a valid enum key. Presented keys: '" .
                implode("', '", array_keys($values)) . "'"
            );
        }

        return $value;
    }

    /**
     * @return array
     */
    public function getValues()
    {
        return $this->values;
    }

    /**
     * @param array $values
     */
    public function setValues($values)
    {
        $this->values = $values;
    }

    /**
     * @return mixed
     */
    public function getDefault()
    {
        return $this->default;
    }

    /**
     * @param mixed $default
     */
    public function setDefault($default)
    {
        $this->default = $default;
    }
}