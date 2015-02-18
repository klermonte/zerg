<?php

namespace Zerg\Field;

use Zerg\DataSet;
use Zerg\Schema;
use Zerg\Schema\SchemaElement;
use Zerg\Stream\AbstractStream;

class Conditional extends SchemaElement
{
    private $path = [];

    public function __construct($path, $properties = [])
    {

    }

    public function setMainParam($keyPath)
    {
        return $this->setPath($keyPath);
    }

    /**
     * @param AbstractStream $stream
     * @return SchemaElement
     * @throws \Exception
     */
    public function parse(AbstractStream $stream)
    {
        $key = $this->getSchemaKey();

        $schemas = $this->getSchemas();
        $schema = null;
        if (array_key_exists($key, $schemas))
            $schema = $schemas[$key];
        elseif ($this->getDefaultSchema() !== null)
            $schema = $this->getDefaultSchema();
        else
            throw new \Exception("Value '{$key}' does not correspond to a valid conditional value");

        if (array_keys(array_keys($schema)) !== array_keys($schema) || is_array(reset($schema))) {
            $schema = new Schema($schema);
        } else {
            $schema = Factory::get($schema);
        }

        return $schema;
    }

    public function setPath($keyPath)
    {
        if (!empty($keyPath)) {
            if (strpos($keyPath, '/') !== false) {
                $this->path = explode('/', trim($keyPath, '/'));
            } else {
                throw new \Exception(print_r($keyPath, 1) . ' is not valid dataset path');
            }
        }
        return $this;
    }

    private function getSchemaKey()
    {
        if ($this->dataSet instanceof DataSet) {
            if (!empty($this->path) && is_array($this->path)) {
                $key = $this->dataSet->getValueByPath($this->path);
            } else {
                throw new \Exception('Wrong dataset path');
            }
        } else {
            throw new \Exception('Dataset required to get value by path');
        }

        return $key;
    }

    private function getSchemas()
    {
        return !empty($this->params['schemas']) ? (array) $this->params['schemas'] : [];
    }

    private function getDefaultSchema()
    {
        return isset($this->params['default']) ? $this->params['default'] : null;
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