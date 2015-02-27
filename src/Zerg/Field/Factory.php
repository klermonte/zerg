<?php

namespace Zerg\Field;

/**
 * Field factory class. Instantiates fields by their declarations.
 *
 * @since 0.1
 * @package Zerg\Field
 */
class Factory
{
    /**
     * Create field instance by its declaration.
     *
     * @param array $elementParams Field declaration.
     * @return AbstractField Field instance.
     * @throws ConfigurationException If invalid declaration is presented.
     */
    public static function get(array $elementParams = [])
    {
        $elementType = array_shift($elementParams);

        $class = "\\Zerg\\Field\\" . ucfirst(strtolower($elementType));
        if (class_exists($class)) {
            return new $class(array_shift($elementParams), array_shift($elementParams));
        }

        throw new ConfigurationException("Field {$elementType} doesn't exist");
    }
}