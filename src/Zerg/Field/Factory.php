<?php

namespace Zerg\Field;

class Factory
{
    /**
     * @param $fieldParams
     * @return array|AbstractField
     * @throws \Exception
     */
    static public function get($fieldParams)
    {
        $return = null;

        $fieldOptions = isset($fieldParams[2]) ? $fieldParams[2] : [];
        if (isset($fieldOptions['multiply'])) {

            $count = (int)$fieldOptions['multiply'];
            unset($fieldParams[2]['multiply']);

            $return = [];
            for ($i = 0; $i < $count; $i++) {
                $return[] = self::get($fieldParams);
            }

        } else {

            $return = self::getInstance($fieldParams[0]);
            $return->setMainParam($fieldParams[1]);
            $return->setParams($fieldOptions);

        }

        return $return;
    }

    /**
     * @param $fieldType
     * @return AbstractField field object
     * @throws \Exception if $fieldType does not exist
     */
    static public function getInstance($fieldType)
    {
        $class = '\\Zerg\\Field\\' . ucfirst(strtolower($fieldType));
        if (class_exists($class)) {
            return new $class;
        } else {
            throw new \Exception("Field class {$class} doesn't exist");
        }
    }
} 