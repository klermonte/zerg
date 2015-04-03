<?php

namespace Zerg\Field;

use Zerg\Stream\AbstractStream;

/**
 * Scalar field represents any simple and atomic part of data.
 *
 * This is an abstract class, so it should be extended by implementation classes.
 * Scalar field should have size {@see $size} which can be overridden by size callback {@see $sizeCallback}
 * and may have count {@see AbstractField::$size}. Also scalar return value can be overridden
 * by formatter {@see $formatter}
 *
 * @since 0.1
 * @package Zerg\Field
 */
abstract class Scalar extends AbstractField
{
    /**
     * @var int|string|callable Size of field in bits/bytes, path to value in DataSet or callback.
     */
    protected $size;

    /**
     * @var callable Callback that changes value of the field.
     */
    protected $formatter;

    /**
     * @var array Human names of some common used sizes.
     */
    private $sizes = [
        'BIT'         => 1,
        'SEMI_NIBBLE' => 2,
        'NIBBLE'      => 4,
        'BYTE'        => 8,
        'SHORT'       => 16,
        'WORD'        => 32,
        'DWORD'       => 64,
    ];

    /**
     * Read part of data from source and return value in necessary format.
     *
     * This is abstract method, so each implementation should return it's own
     * type of value.
     *
     * @param AbstractStream $stream Stream from which read.
     * @return int|string|null Value type depend by implementation.
     */
    abstract public function read(AbstractStream $stream);

    /**
     * Init field size
     *
     * @param array $options
     * @return void
     * @throws ConfigurationException If size option does not present.
     */
    public function init(array $options)
    {
        parent::init($options);
        if (isset($options['size'])) {
            $this->setSize($options['size']);
        } else {
            throw new ConfigurationException('Scalar field must be configured by size');
        }
    }

    /**
     * Return final value of size.
     *
     * If size was set as DataSet path or callback, it will be processed here.
     *
     * @return int Final value of size.
     * @throws ConfigurationException If the value was less than zero.
     */
    public function getSize()
    {
        $size = (int) $this->resolveProperty('size');

        if ($size < 0) {
            throw new ConfigurationException('Field size should not be less 0');
        }

        return $size;
    }

    /**
     * Process and sets size.
     *
     * Size can be represented as a string containing on of size key words {@see $sizes}.
     * Also you can set path to already parsed value in DataSet.
     *
     * @param int|string $size Size in bits/bytes or DataSet path.
     * @return static For chaining.
     */
    public function setSize($size)
    {
        if ($parsed = $this->parseSizeWord($size)) {
            $this->size = $parsed;
        } else {
            $this->size = $size;
        }
        return $this;
    }

    /**
     * Getter for the value callback.
     *
     * @return callable
     */
    public function getFormatter()
    {
        return $this->formatter;
    }

    /**
     * Setter for the value callback.
     *
     * @param callable $formatter
     */
    public function setFormatter($formatter)
    {
        $this->formatter = $formatter;
    }

    /**
     * Reads and process value from Stream.
     *
     * @api
     * @param AbstractStream $stream Stream from which read.
     * @return mixed The final value.
     */
    public function parse(AbstractStream $stream)
    {
        return $this->format($this->read($stream));
    }

    /**
     * Applies all value hooks to read value.
     *
     * @param int|string|null $value Read value.
     * @return mixed Processed value.
     */
    private function format($value)
    {
        if (is_callable($this->formatter)) {
            $value = call_user_func($this->formatter, $value, $this->dataSet);
        }
        return $value;
    }

    /**
     * Process given string and return appropriate size value.
     *
     * @param $word
     * @return int
     */
    private function parseSizeWord($word)
    {
        $sizeWord = strtoupper(preg_replace('/([a-z])([A-Z])/', '$1_$2', $word));
        if (array_key_exists($sizeWord, $this->sizes)) {
            $size = $this->sizes[$sizeWord];
        } else {
            $size = 0;
        }
        return $size;
    }
}