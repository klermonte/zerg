<?php

namespace Zerg\Field;

use Zerg\Stream\AbstractStream;

class String extends AbstractField
{
    public function read(AbstractStream $stream)
    {
        $string = rtrim($stream->read($this->getLength() * 8));

        if ($this->isUtf()) {

            if (strlen($string) < 2)
                return '';

            if (ord($string[0]) == 0xfe && ord($string[1]) == 0xff ||
                ord($string[0]) == 0xff && ord($string[1]) == 0xfe) {
                $string = substr($string, 2);
            }

            while (substr($string, -2) == "\0\0") {
                $string = substr($string, 0, -2);
            }

            return $string;

        }

        return $this->format($string);
    }

    private function isUtf()
    {
        return !empty($this->params['utf']);
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