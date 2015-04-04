<?php

namespace Zerg\Field;


class Arr extends Collection
{
    /**
     * @var int Count of elements.
     */
    protected $count;

    /**
     * @var string|callable
     */
    protected $until;

    /**
     * @var array|AbstractField Field to be repeated.
     */
    protected $field;

    /**
     * @var int
     */
    protected $index;

    public function __construct($count, $field, $options = [])
    {
        $this->setCount($count);
        $this->setField($field);
        $this->index = 0;
        $this->configure($options);
    }

    /**
     * @return int
     */
    public function getCount()
    {
        return (int) $this->resolveProperty('count');
    }

    /**
     * @param int $count
     * @return $this
     */
    public function setCount($count)
    {
        $this->count = $count;
        return $this;
    }

    /**
     * @return callable|string
     */
    public function getUntil()
    {
        return $this->until;
    }

    /**
     * @param callable|string $until
     * @return $this
     */
    public function setUntil($until)
    {
        $this->until = $until;
        return $this;
    }

    /**
     * @return AbstractField
     */
    public function getField()
    {
        $field = $this->resolveProperty('field');
        if (is_array($field)) {
            $field = Factory::get($field);
        }
        return $field;
    }

    /**
     * @param array|AbstractField $field
     * @return $this
     */
    public function setField($field)
    {
        if (is_array($field)) {
            $field = Factory::get($field);
        }
        $this->field = $field;
        return $this;
    }

    /**
     * @inheritdoc
     * @return AbstractField
     */
    public function current()
    {
        return $this->getField();
    }

    /**
     * @inheritdoc
     */
    public function next()
    {
        $this->index++;
    }

    /**
     * @inheritdoc
     */
    public function key()
    {
        return $this->index;
    }

    /**
     * @inheritdoc
     */
    public function valid()
    {
        return $this->index < $this->getCount();
    }

    /**
     * @inheritdoc
     */
    public function rewind()
    {
        $this->index = 0;
    }
}