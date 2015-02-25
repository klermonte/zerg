<?php

namespace Zerg\Field;

use Zerg\Stream\AbstractStream;

class Int extends Scalar
{
    protected $signed = false;

    /**
     * @return mixed
     */
    public function getSigned()
    {
        return $this->signed;
    }

    /**
     * @param mixed $signed
     */
    public function setSigned($signed)
    {
        $this->signed = $signed;
    }

    public function read(AbstractStream $stream)
    {
        $size = $this->getSize();

        $value = null;

        $methodName = 'read' . ($this->signed ? '' : 'U') . 'Int';

        switch ($size) {
            case 8:
            case 16:
            case 32:
                $methodName .= $size;
                $value = $stream->getReader()->$methodName();
                break;

            default:
                if ($size <= 32) {
                    $value = $this->signed
                        ? $stream->getReader()->readBits($size)
                        : $stream->getReader()->readUBits($size);
                } else {
                    new ConfigurationException('Int can not be larger 32 bits');
                }
        }

        return $value;
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