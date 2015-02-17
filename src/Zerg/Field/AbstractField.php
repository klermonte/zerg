<?php
namespace Zerg\Field;

use Zerg\DataSet;
use Zerg\Stream\AbstractStream;

abstract class AbstractField
{
    const BIT         = 1;
    const SEMI_NIBBLE = 2;
    const NIBBLE      = 4;
    const BYTE        = 8;
    const SHORT       = 16;
    const WORD        = 32;
    const DWORD       = 64;

    /**
     * @var int | \Closure
     * */
    protected $length;

    /**
     * @var array
     * */
    protected $params;

    /**
     * @var DataSet
     * */
    protected $dataSet;


    /**
     * @param int | string $param
     * @param array $params
     */
    public function __construct($param = 0, $params = [])
    {
        $this->setMainParam($param);
        $this->setParams($params);
    }

    /**
     * @param int | string $length
     * @return $this
     */
    public function setLength($length)
    {
        $this->length = $length;
        return $this;
    }

    public function setMainParam($value)
    {
        return $this->setLength($value);
    }

    /**
     * @param array $params
     * @return $this
     */
    public function setParams($params = [])
    {
        $this->params = $params;
        return $this;
    }

    /**
     * @param \Zerg\DataSet $dataSet
     * @return $this
     */
    public function setDataSet(DataSet &$dataSet)
    {
        $this->dataSet = $dataSet;
        return $this;
    }

    /**
     * @return int
     * @throws \Exception
     */
    public function getLength()
    {
        if (!is_numeric($this->length)) {
            if ($parsed = $this->parseLengthWord($this->length)) {

                $this->length = $parsed;

            } elseif (strpos($this->length, '/') !== false) {

                if ($this->dataSet instanceof DataSet) {
                    $path = explode('/', trim($this->length, '/'));
                    $this->length = $this->dataSet->getValueByPath($path);
                    return $this->getLength();
                } else {
                    throw new \Exception('Dataset required to get value by path');
                }
            } else {
                throw new \Exception("'{$this->length}' is not valid length value");
            }
        }

        $length = (int) $this->length;

        if (isset($this->params['lengthCallback']) && $this->params['lengthCallback'] instanceof \Closure) {
            $length = (int) $this->params['lengthCallback']($this->length);
        }

        if ($length < 0) {
            throw new \Exception('Field length should not be less 0');
        }

        return $length;
    }

    /**
     * @return array
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * @param string $word human length
     * @return int
     */
    public function parseLengthWord($word)
    {
        $const = strtoupper(preg_replace('/([a-z])([A-Z])/', '$1_$2', $word));
        if (defined('self::' . $const)) {
            $length = constant('self::' . $const);
        } else {
            $length = 0;
        }
        return $length;
    }


    /**
     * @param AbstractStream $stream
     * @return mixed
     */
    abstract public function read(AbstractStream $stream);

    /**
     * @param AbstractStream $stream
     * @param mixed $value
     * @return bool
     */
    abstract public function write(AbstractStream $stream, $value);
}