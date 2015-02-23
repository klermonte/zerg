<?php

namespace Zerg\Stream;

abstract class AbstractStream
{
    /**
     * @var resource The handle of opened file or stream.
     * */
    protected $handle = null;

    /**
     * @var int Current position of internal pointer.
     * */
    protected $position = 0;

    /**
     * @var int Size in bytes on stream.
     * */
    protected $size = 0;

    /**
     * @var string Buffer for storing separate bits while reading not a multiple of eight field size.
     * */
    protected $buffer = '';

    /**
     * @param string $path Path to file.
     */
    public function __construct($path)
    {
        $this->handle = fopen($path, 'rb');
        $this->position = 0;

        fseek($this->handle, 0, SEEK_END);
        $this->size = ftell($this->handle);

        fseek($this->handle, 0);
    }

    /**
     * Read from stream given amount of BITS and return their binary string representation.
     * @param int $bits Amount of bits to read.
     * @return string String representation of read bits.
     * @throws EofException If end of stream has been reached.
     */
    public function read($bits)
    {
        $bufferSize = strlen($this->buffer);

        $readBits = '';
        if ($bufferSize < $bits) {

            $bytes = ceil(($bits - $bufferSize) / 8);

            if ($this->position + $bytes <= $this->size) {

                $this->position += $bytes;
                $readBytes = fread($this->handle, $bytes);

                if (!$bufferSize && !($bits % 8)) {
                    return $readBytes;
                }

                for ($i = 0; $i < $bytes; $i++) {
                    $readBits .= str_pad(base_convert(ord($readBytes[$i]), 10, 2), 8, '0', STR_PAD_LEFT);
                }

            } else {

                throw new EofException('End of file');

            }
        }

        $readBits = $this->buffer . $readBits;

        $data = str_pad(substr($readBits, 0, $bits), ceil($bits / 8) * 8, '0', STR_PAD_LEFT);
        $this->buffer = substr($readBits, $bits);

        $binData = '';
        $eights = str_split($data, 8);
        foreach ($eights as $eight) {
            $binData .= pack('H*', str_pad(base_convert($eight, 2, 16), 2, '0', STR_PAD_LEFT));
        }

        return $binData;

    }

    /**
     * Move internal pointer ahead on given amount of BITS.
     * @param integer $bits Amount of bits to bee skipped.
     * @throws EofException If end of stream has been reached.
     */
    public function skip($bits)
    {
        $bufferSize = strlen($this->buffer);

        if ($bufferSize >= $bits) {

            $this->buffer = substr($this->buffer, $bits);

        } else {

            $bytes = ceil(($bits - $bufferSize) / 8);

            if ($this->position + $bytes <= $this->size) {

                $this->position += $bytes;

                if ( !(($bits - $bufferSize) % 8) ) {

                    fseek($this->handle, $this->position);
                    $this->buffer = '';

                } else {

                    fseek($this->handle, $this->position - 1);
                    $bufferBits = str_pad(base_convert(ord(fread($this->handle, 1)), 10, 2), 8, '0', STR_PAD_LEFT);

                    $this->buffer = substr($bufferBits, ($bits - $bufferSize) % 8);

                }

            } else {

                throw new EofException('End of file');

            }
        }
    }


    /**
     * Release resources on object destruction.
     */
    public function __destruct()
    {
        fclose($this->handle);
    }
} 