<?php

namespace Zerg\Field;

use Zerg\Stream\AbstractStream;

class Int extends Scalar
{
    protected $signed;

    public function read(AbstractStream $stream)
    {
        $size = $this->getSize();

        $raw = $stream->read($size);

        $value = null;

        if ($size <= 8) {
            $value = $this->signed
                ? $this->int8($raw)
                : $this->uInt8($raw);
        } else {
            throw new \Exception('Integer longer 8 bits not implemented yet');
        }

        return $value;
    }

    private function int8($data)
    {
        $ord = ord($data);
        return ($ord > 127)
            ? -$ord - 2 * (128 - $ord)
            : $ord;
    }

    private function uInt8($data)
    {
        return ord($data);
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