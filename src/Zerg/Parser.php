<?php

namespace Zerg;

use Zerg\Field\AbstractField;
use Zerg\Field\Conditional;
use Zerg\Stream\AbstractStream;

class Parser
{
    protected $stream;
    protected $dataSet;

    public function __construct(AbstractStream $stream)
    {
        $this->stream = $stream;
        $this->dataSet = new DataSet;
    }

    public function parse($schema)
    {
        foreach ($schema as $fieldName => $field) {

            while ($field instanceof Conditional) {
                $field->setDataSet($this->dataSet);
                $field = $field->read($this->stream);
            }

            if ($field instanceof AbstractField) {

                $field->setDataSet($this->dataSet);

                $value = $field->read($this->stream);
                if ($value !== null) {
                    $this->dataSet->setValue($fieldName, $value);
                }

            } elseif (is_array($field) || $field instanceof Schema) {

                // deep into data set
                $this->dataSet->push($fieldName);

                // recursively parse sub schema
                $this->parse($field);

                // get up back to previous level
                $this->dataSet->pop();

            } else {

                throw new \Exception('Unknown schema element ' . print_r($field, true));

            }

        }

        return $this->dataSet;
    }
} 