<?php

namespace ITE\FormBundle\Validation\Constraints;

/**
 * Class EqualTo
 *
 * @Annotation
 * @Target({"PROPERTY"})
 *
 * @see Symfony\Component\Validator\Constraints\EqualTo
 * @see Symfony\Component\Validator\Constraints\EqualToValidator
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class EqualTo extends AbstractComparison
{
    public $message = 'This value should be equal to {{ compared_value }}.';
}