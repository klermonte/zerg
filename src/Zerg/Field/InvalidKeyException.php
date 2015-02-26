<?php

namespace Zerg\Field;

/**
 * This exception throws when filed with determined list of values
 * like Enum {@see Enum} or Conditional {@see Conditional} could not
 * find key read from Stream and have not default value.
 * */
class InvalidKeyException extends \UnexpectedValueException
{
}