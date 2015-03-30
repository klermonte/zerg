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
     */
    public function read(AbstractStream $stream)
    {
        return $stream->getReader()->readInt($this->getSize(), $this->signed);
    }
}