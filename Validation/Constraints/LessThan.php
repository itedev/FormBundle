<?php

namespace ITE\FormBundle\Validation\Constraints;

/**
 * Class LessThan
 *
 * @Annotation
 * @Target({"PROPERTY"})
 *
 * @see Symfony\Component\Validator\Constraints\LessThan
 * @see Symfony\Component\Validator\Constraints\LessThanValidator
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class LessThan extends AbstractComparison
{
    public $message = 'This value should be less than {{ compared_value }}.';
}