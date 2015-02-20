<?php

namespace Zerg\Field;

class Factory
{
    /**
     * @param array $elementParams
     * @return \Zerg\SchemaElement
     * @throws \Exception
     */
    public static function get($elementParams = [])
    {
        $elementType = array_shift($elementParams);

        $nameSpaces = ['Field\\', ''];
        foreach ($nameSpaces as $nameSpace) {
            $class = "\\Zerg\\{$nameSpace}" . ucfirst(strtolower($elementType));
            if (class_exists($class)) {
                return new $class(array_shift($elementParams), array_shift($elementParams));
            }
        }

        throw new \Exception("Field {$elementType} doesn't exist");

    }
}