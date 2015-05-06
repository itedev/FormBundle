<?php

namespace ITE\FormBundle\Validation\Constraints;

/**
 * Class NotEqualTo
 *
 * @Annotation
 * @Target({"PROPERTY"})
 *
 * @see Symfony\Component\Validator\Constraints\NotEqualTo
 * @see Symfony\Component\Validator\Constraints\NotEqualToValidator
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class NotEqualTo extends AbstractComparison
{
    public $message = 'This value should not be equal to {{ compared_value }}.';
}