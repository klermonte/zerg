<?php

namespace Zerg\Field;

use Zerg\DataSet;
use Zerg\Stream\AbstractStream;

class Conditional extends AbstractField
{
    protected $path = '';
    protected $fields = [];
    protected $default = null;

    public function __construct($path, $properties = [])
    {
        $this->path = $path;
        $this->configure($properties);
    }

    /**
     * @param AbstractStream $stream
     * @return AbstractField
     * @throws \Exception
     */
    public function parse(AbstractStream $stream)
    {
        $key = $this->getSchemaKey();

        if (array_key_exists($key, $this->fields))
            $field = $this->fields[$key];
        elseif ($this->default !== null)
            $field = $this->default;
        else
            throw new \Exception("Value '{$key}' does not correspond to a valid conditional value");

        $isAssoc = array_keys(array_keys($field)) !== array_keys($field);

        if ($isAssoc || is_array(reset($field))) {
            $field = ['collection', $field];
        }

        $field = Factory::get($field);
        $field->setDataSet($this->getDataSet());

        return $field;
    }

    private function getSchemaKey()
    {
        if ($this->getDataSet() instanceof DataSet) {
            if (!empty($this->path)) {
                $key = $this->dataSet->getValueByPath($this->dataSet->parsePath($this->path));
            } else {
                throw new \Exception('Wrong dataset path');
            }
        } else {
            throw new \Exception('Dataset required to get value by path');
        }

        return $key;
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