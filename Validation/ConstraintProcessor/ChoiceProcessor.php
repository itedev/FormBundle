<?php

namespace ITE\FormBundle\Validation\ConstraintProcessor;

use ITE\FormBundle\Validation\AbstractConstraintProcessor;
use ITE\FormBundle\Validation\ClientConstraint;
use ITE\FormBundle\Validation\Constraints\Choice;

/**
 * Class ChoiceProcessor
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class ChoiceProcessor extends AbstractConstraintProcessor
{
    /**
     * {@inheritdoc}
     */
    public function supports(ClientConstraint $constraint)
    {
        return $constraint instanceof Choice;
    }

    /**
     * {@inheritdoc}
     */
    public function process(ClientConstraint $constraint)
    {
        /** @var $constraint Choice */
        $constraint->multipleMessage = $this->translate($constraint->multipleMessage);
        $constraint->minMessage = $this->translate($constraint->minMessage, [
            '{{ limit }}' => $constraint->min
        ], (int) $constraint->min);
        $constraint->maxMessage = $this->translate($constraint->maxMessage, [
            '{{ limit }}' => $constraint->max
        ], (int) $constraint->max);
        $constraint->message = $this->translate($constraint->message);
    }
}
