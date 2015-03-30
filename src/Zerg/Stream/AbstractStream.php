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
     * @var \PhpBio\BitBuffer Object that reads data from file|memory stream.
     * */
    protected $reader;

    /**
     * Implementations should init reader itself by given value.
     *
     * @param string $path Value to init reader.
     */
    abstract public function __construct($path);

    /**
     * Getter for $reader property.
     *
     * @return \PhpBio\BitBuffer Object that reads data from file|memory stream.
     */
    public function getReader()
    {
        return $this->reader;
    }


    /**
     * Move internal pointer by given amount of bits ahead without reading dta.
     *
     * @param int $size Amount of bits to be skipped.
     */
    public function skip($size)
    {
        $this->reader->setPosition($this->reader->getPosition() + $size);
    }

//    /**
//     * Release resources on object destruction.
//     */
//    public function __destruct()
//    {
//        fclose($this->reader->getInputHandle());
//    }
}