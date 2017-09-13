<?php

namespace ITE\FormBundle\Validation\Constraints;

use ITE\FormBundle\Validation\ClientConstraint;

/**
 * Class IsFalse
 *
 * @Annotation
 * @Target({"PROPERTY"})
 *
 * @see Symfony\Component\Validator\Constraints\IsFalse
 * @see Symfony\Component\Validator\Constraints\FalseValidator
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class IsFalse extends ClientConstraint
{
    public $message = 'This value should be false.';
}
