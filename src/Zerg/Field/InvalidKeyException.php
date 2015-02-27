<?php

namespace Zerg\Field;

/**
 * This exception throws when filed with determined list of values
 * like Enum {@see Enum} or Conditional {@see Conditional} could not
 * find key read from Stream and has not default value.
 *
 * @since 0.1
 * @package Zerg\Field
 */
class InvalidKeyException extends \UnexpectedValueException
{
}