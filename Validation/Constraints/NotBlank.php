<?php

namespace ITE\FormBundle\Validation\Constraints;

use ITE\FormBundle\Validation\ClientConstraint;

/**
 * Class NotBlank
 *
 * @Annotation
 * @Target({"PROPERTY"})
 *
 * @see Symfony\Component\Validator\Constraints\NotBlank
 * @see Symfony\Component\Validator\Constraints\NotBlankValidator
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class NotBlank extends ClientConstraint
{
    public $message = 'This value should not be blank.';
}
