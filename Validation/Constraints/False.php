<?php

namespace ITE\FormBundle\Validation\Constraints;

use ITE\FormBundle\Validation\ClientConstraint;

/**
 * Class False
 *
 * @Annotation
 * @Target({"PROPERTY"})
 *
 * @see Symfony\Component\Validator\Constraints\False
 * @see Symfony\Component\Validator\Constraints\FalseValidator
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class False extends ClientConstraint
{
    public $message = 'This value should be false.';
}