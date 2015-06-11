<?php

namespace ITE\FormBundle\Validation\Constraints;

use ITE\FormBundle\Validation\ClientConstraint;

/**
 * Class Type
 *
 * @Annotation
 * @Target({"PROPERTY"})
 *
 * @see Symfony\Component\Validator\Constraints\Type
 * @see Symfony\Component\Validator\Constraints\TypeValidator
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class Type extends ClientConstraint
{
    public $message = 'This value should be of type {{ type }}.';
    public $type;

    /**
     * {@inheritdoc}
     */
    public function getDefaultOption()
    {
        return 'type';
    }

    /**
     * {@inheritdoc}
     */
    public function getRequiredOptions()
    {
        return ['type'];
    }
}