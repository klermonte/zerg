<?php

namespace Zerg\Field;

use Zerg\DataSet;
use Zerg\Stream\AbstractStream;

class Conditional extends AbstractField
{
    protected $key = null;
    protected $fields = [];
    protected $default = null;

    public function setMainParam($key)
    {
        $this->key = $key;
    }

    /**
     * @param AbstractStream $stream
     * @return mixed
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
     * @return AbstractField
     * @throws InvalidKeyException
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

    /**
     * @param AbstractStream $stream
     * @param mixed $value
     * @return bool
     */
    public function write(AbstractStream $stream, $value)
    {
        // TODO: Implement write() method.
    }
}