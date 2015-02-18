<?php

namespace Zerg\Schema;

trait SizeableTrait 
{
    private $sizes = [
        'BIT'         => 1,
        'SEMI_NIBBLE' => 2,
        'NIBBLE'      => 4,
        'BYTE'        => 8,
        'SHORT'       => 16,
        'WORD'        => 32,
        'DWORD'       => 64,
    ];

    protected $size;
    protected $sizeCallback;
    
    public function setSize($size)
    {
        $this->size = $size;
    }

    public function getSize()
    {
        if (!is_numeric($this->size)) {
            if ($parsed = $this->parseSizeWord($this->size)) {

                $this->size = $parsed;

            } elseif (strpos($this->size, '/') !== false) {

                /*if ($this->dataSet instanceof DataSet) {
                    $path = explode('/', trim($this->size, '/'));
                    $this->size = $this->dataSet->getValueByPath($path);
                    return $this->getSize();
                } else {
                    throw new \Exception('Dataset required to get value by path');
                }*/
            } else {
                throw new \Exception("'{$this->size}' is not valid size value");
            }
        }

        $size = (int) $this->size;

        if (is_callable($this->sizeCallback)) {
            $size = call_user_func($this->sizeCallback, $size);
        }

        if ($size < 0) {
            throw new \Exception('Element size should not be less 0');
        }

        return $size;
    }

    public function parseSizeWord($word)
    {
        $size = strtoupper(preg_replace('/([a-z])([A-Z])/', '$1_$2', $word));
        if (array_key_exists($size, $this->sizes)) {
            $size = $this->sizes[$size];
        } else {
            $size = 0;
        }
        return $size;
    }
}