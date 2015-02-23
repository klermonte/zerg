<?php

namespace Zerg\Stream;

class StringStream extends AbstractStream
{
    public function __construct($string)
    {
        if (($this->handle = fopen('php://memory', 'w+b')) === false) {
            throw new MemoryStreamException('Unable to open php://memory stream');
        }

        if ($string !== null && is_string($string)) {

            if (($this->size = fwrite($this->handle, $string, strlen($string))) === false) {
                throw new MemoryStreamException('Unable to write data to php://memory stream');
            }

            fseek($this->handle, 0);
        }
    }

} 