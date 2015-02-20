<?php

namespace Zerg\Field;

class Factory
{
    /**
     * @param array $elementParams
     * @return AbstractField
     * @throws \Exception
     */
    public static function get($elementParams = [])
    {
        $elementType = array_shift($elementParams);

        $class = "\\Zerg\\Field" . ucfirst(strtolower($elementType));
        if (class_exists($class)) {
            return new $class(array_shift($elementParams), array_shift($elementParams));
        }

        throw new \Exception("Field {$elementType} doesn't exist");

    }
}