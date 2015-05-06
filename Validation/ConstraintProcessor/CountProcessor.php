<?php

namespace ITE\FormBundle\Validation\ConstraintProcessor;

use ITE\FormBundle\Validation\AbstractConstraintProcessor;
use ITE\FormBundle\Validation\Constraint;
use ITE\FormBundle\Validation\Constraints\Count;

/**
 * Class CountProcessor
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class CountProcessor extends AbstractConstraintProcessor
{
    /**
     * {@inheritdoc}
     */
    public function supports(Constraint $constraint)
    {
        return $constraint instanceof Count;
    }

    /**
     * {@inheritdoc}
     */
    public function process(Constraint $constraint)
    {
        /** @var $constraint Count */
        $constraint->exactMessage = $this->translate($constraint->exactMessage, [
            '{{ limit }}' => $constraint->max
        ], (int) $constraint->max);
        $constraint->maxMessage = $this->translate($constraint->maxMessage, [
            '{{ limit }}' => $constraint->max
        ], (int) $constraint->max);
        $constraint->minMessage = $this->translate($constraint->minMessage, [
            '{{ limit }}' => $constraint->min
        ], (int) $constraint->min);
    }

}