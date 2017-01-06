<?php

namespace ITE\FormBundle\Validation\Constraints;

/**
 * Class LessThanOrEqual
 *
 * @Annotation
 * @Target({"PROPERTY"})
 *
 * @see Symfony\Component\Validator\Constraints\LessThanOrEqual
 * @see Symfony\Component\Validator\Constraints\LessThanOrEqualValidator
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class LessThanOrEqual extends AbstractComparison
{
    public $message = 'This value should be less than or equal to {{ compared_value }}.';
}
