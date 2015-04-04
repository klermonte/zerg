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
     * @param array $declaration Field declaration.
     * @return AbstractField Field instance.
     * @throws ConfigurationException If invalid declaration is presented.
     */
    private static function instantiate(array $declaration)
    {
        $fieldType = array_shift($declaration);

        $class = "\\Zerg\\Field\\" . ucfirst(strtolower($fieldType));
        if (class_exists($class)) {
            $reflection = new \ReflectionClass($class);
            return $reflection->newInstanceArgs($declaration);
        }

        throw new ConfigurationException("Field {$fieldType} doesn't exist");
    }

    public static function get($array)
    {
        if (!is_array($array)) {
            throw new ConfigurationException('Unknown element declaration');
        }

        $isAssoc = array_keys(array_keys($array)) !== array_keys($array);

        if ($isAssoc || is_array(reset($array))) {
            $array = ['collection', $array];
        }

        return Factory::instantiate($array);
    }
}