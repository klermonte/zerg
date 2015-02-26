<?php

namespace Zerg\Field;

use Zerg\Stream\AbstractStream;

/**
 * String represents string type data.
 *
 * Data, parsed by this type of field returns as it is in binary file.
 * @since 0.1
 * */
class String extends Scalar
{
    /**
     * @var bool Whether returned string should process like UTF-8 encoded.
     * */
    protected $utf;

    /**
     * Read string from stream as it is.
     *
     * @param AbstractStream $stream Stream from which read.
     * @return string Returned string.
     */
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