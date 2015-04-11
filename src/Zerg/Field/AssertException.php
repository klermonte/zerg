<?php

namespace Zerg\Field;

/**
 * This exception throws when parsed or stored value fail assertion {@see AbstractField::validate()}.
 *
 * @since 1.0
 * @package Zerg\Field
 */
class AssertException extends \UnexpectedValueException
{
}