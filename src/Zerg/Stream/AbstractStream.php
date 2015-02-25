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
        $newPositionInBits = $this->reader->getPosition() * 8 + $this->reader->getCurrentBit() + $size;
        $this->reader->setPosition(intval($newPositionInBits / 8));
        $this->reader->setCurrentBit($newPositionInBits % 8);
    }

    /**
     * Release resources on object destruction.
     */
    public function __destruct()
    {
        fclose($this->reader->getInputHandle());
    }
}