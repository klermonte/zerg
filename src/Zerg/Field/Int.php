<?php

namespace Zerg\Field;

use Zerg\Stream\AbstractStream;

/**
 * Int field read data from Stream and cast it to integer.
 *
 * @since 0.1
 * @package Zerg\Field
 */
class Int extends Scalar
{
    /**
     * @var bool Whether field is signed. If so, value form stream will be casted to signed integer.
     */
    protected $signed = false;

    /**
     * Getter for signed property.
     *
     * @return bool
     */
    public function getSigned()
    {
        return $this->signed;
    }

    /**
     * Setter for signed property.
     *
     * @param bool $signed
     */
    public function setSigned($signed)
    {
        $this->signed = $signed;
    }

    /**
     * Read data from Stream and cast it to integer.
     *
     * @param AbstractStream $stream Stream from which read.
     * @return int Result value.
     * @throws ConfigurationException if unsupported size is resolved.
     */
    public function read(AbstractStream $stream)
    {
        $size = $this->getSize();

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
                    throw new ConfigurationException('Int can not be larger 32 bits');
                }
        }

        return $value;
    }
}