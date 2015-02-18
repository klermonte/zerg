<?php

namespace Zerg\Schema;

trait CountableTrait
{
    protected $count;
    protected $countCallback;

    public function getCount()
    {
        return $this->count;
    }

    public function setCount($count)
    {
        if (!is_numeric($this->count)) {
            if (strpos($this->count, '/') !== false) {

                /*if ($this->dataSet instanceof DataSet) {
                    $path = explode('/', trim($this->size, '/'));
                    $this->size = $this->dataSet->getValueByPath($path);
                    return $this->getSize();
                } else {
                    throw new \Exception('Dataset required to get value by path');
                }*/
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