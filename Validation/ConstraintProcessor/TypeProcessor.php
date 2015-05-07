<?php

namespace ITE\FormBundle\Validation\ConstraintProcessor;

use ITE\FormBundle\Validation\AbstractConstraintProcessor;
use ITE\FormBundle\Validation\Constraint;
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
    public function supports(Constraint $constraint)
    {
        return $constraint instanceof Type;
    }

    /**
     * {@inheritdoc}
     */
    public function process(Constraint $constraint)
    {
        /** @var $constraint Type */
        $constraint->message = $this->translate($constraint->message, [
            '{{ type }}', $constraint->type
        ]);
    }

}