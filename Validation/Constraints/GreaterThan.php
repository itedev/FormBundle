<?php

namespace ITE\FormBundle\Validation\Constraints;

/**
 * Class GreaterThan
 *
 * @Annotation
 * @Target({"PROPERTY"})
 *
 * @see Symfony\Component\Validator\Constraints\GreaterThan
 * @see Symfony\Component\Validator\Constraints\GreaterThanValidator
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class GreaterThan extends AbstractComparison
{
    public $message = 'This value should be greater than {{ compared_value }}.';
}
