<?php

namespace ITE\FormBundle\Validation\ConstraintProcessor;

use ITE\FormBundle\Validation\AbstractConstraintProcessor;
use ITE\FormBundle\Validation\ClientConstraint;
use ITE\FormBundle\Validation\Constraints\Range;

/**
 * Class RangeProcessor
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class RangeProcessor extends AbstractConstraintProcessor
{
    /**
     * {@inheritdoc}
     */
    public function supports(ClientConstraint $constraint)
    {
        return $constraint instanceof Range;
    }

    /**
     * {@inheritdoc}
     */
    public function process(ClientConstraint $constraint)
    {
        /** @var $constraint Range */
        $constraint->invalidMessage = $this->translate($constraint->invalidMessage);
        $constraint->maxMessage = $this->translate($constraint->maxMessage, [
            '{{ limit }}' => $this->formatValue($constraint->max, self::PRETTY_DATE)
        ]);
        $constraint->minMessage = $this->translate($constraint->minMessage, [
            '{{ limit }}' => $this->formatValue($constraint->min, self::PRETTY_DATE)
        ]);
    }

}