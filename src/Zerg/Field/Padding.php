<?php

namespace Zerg\Field;

use Zerg\Stream\AbstractStream;

/**
 * Padding field helps to skip some data not reading it.
 *
 * Field not designed to return some value and save it.
 * So in parsed DataSet there will not this field.
 * But given amount of data will be skipped.
 *
 * @since 0.1
 * @package Zerg\Field
 */
class Padding extends Scalar
{
    /**
     * Tells current Stream to skip given amount of bits.
     *
     * @param AbstractStream $stream Stream which which should skip data.
     * @return null To detect that no value has been read.
     */
    public function read(AbstractStream $stream)
    {
        $stream->skip($this->getSize());
        return null;
    }
}