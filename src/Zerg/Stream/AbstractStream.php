<?php

namespace Zerg\Stream;

abstract class AbstractStream
{
    protected $handle = null;
    protected $position = 0;
    protected $size = 0;
    protected $buffer = '';

    public function __construct($path)
    {
        $this->handle = fopen($path, 'rb');
        $this->position = 0;

        fseek($this->handle, 0, SEEK_END);
        $this->size = ftell($this->handle);

        fseek($this->handle, 0);
    }

    public function read($bits)
    {
        $bufferSize = strlen($this->buffer);

        $readBits = '';
        if ($bufferSize < $bits) {

            $bytes = ceil(($bits - $bufferSize) / 8);

            if ($this->position + $bytes <= $this->size) {

                $this->position += $bytes;
                $readBytes = fread($this->handle, $bytes);

                if (!$bufferSize && !($bits % 8))
                    return $readBytes;

                for($i = 0; $i < $bytes; $i++)
                    $readBits .= str_pad(base_convert(ord($readBytes[$i]), 10, 2), 8, '0', STR_PAD_LEFT);

            } else {

                throw new \Exception('End of file');

            }
        }

        $readBits = $this->buffer . $readBits;

        $data = str_pad(substr($readBits, 0, $bits), ceil($bits / 8) * 8, '0', STR_PAD_LEFT);
        $this->buffer = substr($readBits, $bits);

        $binData = '';
        $eights = str_split($data, 8);
        foreach ($eights as $eight)
            $binData .= pack('H*', str_pad(base_convert($eight, 2, 16), 2, '0', STR_PAD_LEFT));

        return $binData;

    }

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

                throw new \Exception('End of file');

            }
        }
    }

    public function __destruct()
    {
        fclose($this->handle);
    }
} 