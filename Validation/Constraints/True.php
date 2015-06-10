<?php

namespace ITE\FormBundle\Validation\Constraints;

use ITE\FormBundle\Validation\ClientConstraint;

/**
 * Class True
 *
 * @Annotation
 * @Target({"PROPERTY"})
 *
 * @see Symfony\Component\Validator\Constraints\True
 * @see Symfony\Component\Validator\Constraints\TrueValidator
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class True extends ClientConstraint
{
    public $message = 'This value should be true.';
}