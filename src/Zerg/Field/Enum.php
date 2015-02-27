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
    protected $values = [];

    /**
     * @var mixed Default value, if no one of possible values are not relate to read value.
     */
    protected $default = null;

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
        $values = !empty($this->values) ? (array) $this->values : [];

        if (array_key_exists($key, $values)) {
            $value = $values[$key];
        } else {
            $value = $this->default;
        }

        if (is_null($value)) {
            throw new InvalidKeyException(
                "Value '{$key}' does not correspond to a valid enum key. Presented keys: '" .
                implode("', '", array_keys($values)) . "'"
            );
        }

        return $value;
    }
}