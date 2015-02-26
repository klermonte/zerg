<?php

namespace Zerg\Field;

/**
 * This exception throws when field gets invalid configuration
 * value and can't cast it to valid.
 * */
class ConfigurationException extends \InvalidArgumentException
{
}