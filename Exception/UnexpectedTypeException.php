<?php

namespace ITE\FormBundle\Exception;

/**
 * Class UnexpectedTypeException
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class UnexpectedTypeException extends InvalidArgumentException
{
    /**
     * @param mixed $value
     * @param string $expectedType
     */
    public function __construct($value, $expectedType)
    {
        parent::__construct(sprintf(
            'Expected argument of type "%s", "%s" given',
            $expectedType,
            is_object($value) ? get_class($value) : gettype($value)
        ));
    }
}
