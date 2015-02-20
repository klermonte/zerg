<?php

namespace Zerg\Field;
use Zerg\DataSet;

/**
 * @property \Zerg\Schema parent
 * */
trait CountableTrait
{
    protected $count = 1;
    protected $countCallback;

    public function setCount($count)
    {
        $this->count = $count;
    }

    public function getCount()
    {
        if (!is_numeric($this->count)) {
            if (strpos($this->count, '/') !== false) {

                if (($dataSet = $this->parent->getDataSet()) instanceof DataSet) {
                    $this->count = $dataSet->getValueByPath($this->count);
                    return $this->getCount();
                } else {
                    throw new \Exception('Dataset required to get value by path');
                }
            } else {
                throw new \Exception("'{$this->count}' is not valid count value");
            }
        }

        $count = (int) $this->count;

        if (is_callable($this->countCallback)) {
            $count = call_user_func($this->countCallback, $count);
        }

        if ($count < 0) {
            throw new \Exception('Element count should not be less 0');
        }

        return $count;
    }
}