<?php

namespace Zerg\Field;

/**
 * This exception throws when field gets invalid configuration
 * value and can't cast it to valid.
 *
 * @since 0.1
 * @package Zerg\Field
 */
class ConfigurationException extends \InvalidArgumentException
{
}