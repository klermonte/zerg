<?php

namespace Zerg\Stream;

abstract class AbstractStream
{
    /**
     * @var \PhpBinaryReader\BinaryReader
     * */
    protected $reader;

    abstract public function __construct($path);

    /**
     * @return \PhpBinaryReader\BinaryReader
     */
    public function getReader()
    {
        return $this->reader;
    }


    /**
     * @param int $size Amount of bits to be skipped
     */
    public function skip($size)
    {
        $newBits = $size % 8;
        if (!$this->reader->getCurrentBit() && !$newBits) {
            $this->reader->setPosition($this->reader->getPosition() + $size / 8);
        } else {
            $this->reader->readUBits($size);
        }
    }

    /**
     * Release resources on object destruction.
     */
    public function __destruct()
    {
        fclose($this->reader->getInputHandle());
    }
}