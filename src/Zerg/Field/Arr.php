<?php

namespace Zerg\Field;


use Zerg\Stream\AbstractStream;

class Arr extends AbstractField
{
    /**
     * @var int Count of elements.
     */
    protected $count;

    /**
     * @var AbstractField Field to be repeated.
     */
    protected $field;

    /**
     * Init field by it's count of elements.
     *
     * @param int|string $count Size in  or DataSet path.
     */
    public function init($count)
    {
        $this->count = $count;
    }

    /**
     * Read and process data from Stream.
     *
     * @api
     * @param AbstractStream $stream Stream from which field should read.
     * @return mixed Processed value.
     */
    public function parse(AbstractStream $stream)
    {
        // TODO: Implement parse() method.
    }
}