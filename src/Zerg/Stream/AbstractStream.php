<?php

namespace Zerg\Stream;

/**
 * AbstractStream represents any type of stream - an entity that wraps data source and
 * encapsulates the read and write operations.
 *
 * @since 0.1
 * @package Zerg\Stream
 */
abstract class AbstractStream
{
    /**
     * @var \PhpBio\BitBuffer Object that reads and writes data from|to file|memory stream.
     * */
    protected $buffer;

    /**
     * Implementations should init buffer itself by given value.
     *
     * @param string $path Value to init buffer.
     */
    abstract public function __construct($path);

    /**
     * Getter for $buffer property.
     *
     * @return \PhpBio\BitBuffer Object that reads data from file|memory stream.
     */
    public function getBuffer()
    {
        return $this->buffer;
    }

    /**
     * Move internal pointer by given amount of bits ahead without reading dta.
     *
     * @param int $size Amount of bits to be skipped.
     */
    public function skip($size)
    {
        $this->buffer->setPosition($this->buffer->getPosition() + $size);
    }
}