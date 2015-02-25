<?php

namespace Zerg\Field;

use Zerg\Stream\AbstractStream;

class String extends Scalar
{
    protected $utf;

    public function read(AbstractStream $stream)
    {
        $string = $stream->getReader()->readString($this->getSize());

        if ($this->utf) {

            if (strlen($string) < 2) {
                return '';
            }

            if (ord($string[0]) == 0xfe && ord($string[1]) == 0xff ||
                ord($string[0]) == 0xff && ord($string[1]) == 0xfe) {
                $string = substr($string, 2);
            }

            return $string;

        }

        return $this->format($string);
    }
}