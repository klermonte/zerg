<?php

namespace Zerg\Schema;

class Factory
{
    /**
     * @param array $elementParams
     * @return SchemaElement
     * @throws \Exception
     */
    public static function get($elementParams = [])
    {
        $elementType = array_shift($elementParams);

        $nameSpaces = ['Field', 'Schema'];
        foreach ($nameSpaces as $nameSpace) {
            $class = "\\Zerg\\{$nameSpace}\\" . ucfirst(strtolower($elementType));
            if (class_exists($class)) {
                return new $class(array_shift($elementParams), array_shift($elementParams));
            }
        }

        throw new \Exception("Field class {$class} doesn't exist");

    }
}