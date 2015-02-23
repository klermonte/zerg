<?php

namespace Zerg\Field;

use Zerg\Stream\AbstractStream;

class Enum extends Int
{
    protected $values;
    protected $default;

    public function read(AbstractStream $stream)
    {
        $key = parent::read($stream);

        $values = $this->getValues();

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

    private function getValues()
    {
        return !empty($this->values) ? (array) $this->values : [];
    }

    public function write(AbstractStream $stream, $value)
    {

    }
} 