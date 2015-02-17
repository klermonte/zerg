<?php

namespace Zerg\Field;

use Zerg\Stream\AbstractStream;

class Enum extends Int
{
    public function read(AbstractStream $stream)
    {
        $key = parent::read($stream);

        $values = $this->getValues();
        $value = null;
        if (array_key_exists($key, $values))
            $value = $values[$key];
        elseif ($this->getDefaultValue() !== null)
            $value = $this->getDefaultValue();
        else
            throw new \Exception(
                "Value '{$key}' does not correspond to a valid enum value. Given options: ".
                print_r($values, true)
            );

        return $value;
    }

    private function getValues()
    {
        return !empty($this->params['values']) ? (array) $this->params['values'] : [];
    }

    private function getDefaultValue()
    {
        return isset($this->params['default']) ? $this->params['default'] : null;
    }

    public function write(AbstractStream $stream, $value)
    {

    }
} 