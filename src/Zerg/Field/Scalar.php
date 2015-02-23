<?php

namespace Zerg\Field;

use Zerg\Stream\AbstractStream;

abstract class Scalar extends AbstractField
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
    protected $valueCallback;

    /**
     * @return mixed
     */
    public function getValueCallback()
    {
        return $this->valueCallback;
    }

    /**
     * @param mixed $valueCallback
     */
    public function setValueCallback($valueCallback)
    {
        $this->valueCallback = $valueCallback;
    }

    abstract public function read(AbstractStream $stream);

    public function setMainParam($size)
    {
        $this->setSize($size);
    }

    public function parse(AbstractStream $stream)
    {
        return $this->format($this->read($stream));
    }

    public function format($value)
    {
        if (is_callable($this->valueCallback)) {
            $value = call_user_func($this->valueCallback, $value);
        }
        return $value;
    }

    public function setSize($size)
    {
        if ($parsed = $this->parseSizeWord($size)) {
            $this->size = $parsed;
        } else {
            $this->size = $size;
        }
    }

    public function getSize()
    {
        $this->resolveProperty('size');
        $size = $this->getCallbackableProperty('size');

        if ($size < 0) {
            throw new ConfigurationException('Element size should not be less 0');
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