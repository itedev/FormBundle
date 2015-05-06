<?php

namespace ITE\FormBundle\Validation\Constraints;

/**
 * Class GreaterThanOrEqual
 *
 * @Annotation
 * @Target({"PROPERTY"})
 *
 * @see Symfony\Component\Validator\Constraints\GreaterThanOrEqual
 * @see Symfony\Component\Validator\Constraints\GreaterThanOrEqualValidator
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class GreaterThanOrEqual extends AbstractComparison
{
    public $message = 'This value should be greater than or equal to {{ compared_value }}.';
}