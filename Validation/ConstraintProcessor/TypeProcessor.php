<?php

namespace ITE\FormBundle\Validation\ConstraintProcessor;

use ITE\FormBundle\Validation\AbstractConstraintProcessor;
use ITE\FormBundle\Validation\ClientConstraint;
use ITE\FormBundle\Validation\Constraints\Type;

/**
 * Class TypeProcessor
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class TypeProcessor extends AbstractConstraintProcessor
{
    /**
     * {@inheritdoc}
     */
    public function supports(ClientConstraint $constraint)
    {
        return $constraint instanceof Type;
    }

    /**
     * {@inheritdoc}
     */
    public function process(ClientConstraint $constraint)
    {
        /** @var $constraint Type */
        $constraint->message = $this->translate($constraint->message, [
            '{{ type }}', $constraint->type
        ]);
    }
}
