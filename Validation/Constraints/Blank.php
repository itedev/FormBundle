<?php

namespace ITE\FormBundle\Validation\Constraints;

use ITE\FormBundle\Validation\ClientConstraint;

/**
 * Class Blank
 *
 * @Annotation
 * @Target({"PROPERTY"})
 *
 * @see Symfony\Component\Validator\Constraints\Blank
 * @see Symfony\Component\Validator\Constraints\BlankValidator
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class Blank extends ClientConstraint
{
    public $message = 'This value should be blank.';
}