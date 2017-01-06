<?php

namespace ITE\FormBundle\Validation\ConstraintProcessor;

use ITE\FormBundle\Validation\AbstractConstraintProcessor;
use ITE\FormBundle\Validation\ClientConstraint;
use ITE\FormBundle\Validation\Constraints\AbstractComparison;

/**
 * Class AbstractComparisonProcessor
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class AbstractComparisonProcessor extends AbstractConstraintProcessor
{
    /**
     * {@inheritdoc}
     */
    public function supports(ClientConstraint $constraint)
    {
        return $constraint instanceof AbstractComparison;
    }

    /**
     * {@inheritdoc}
     */
    public function process(ClientConstraint $constraint)
    {
        /** @var $constraint AbstractComparison */
        $constraint->message = $this->translate($constraint->message, [
            '{{ value }}' => $this->formatValue($constraint->value, self::OBJECT_TO_STRING | self::PRETTY_DATE),
            '{{ compared_value }}' => $this->formatValue($constraint->value, self::OBJECT_TO_STRING | self::PRETTY_DATE),
            '{{ compared_value_type }}' => $this->formatTypeOf($constraint->value)
        ]);
    }
}
