<?php

namespace ITE\FormBundle\Validation\ConstraintNormalizer;

use ITE\FormBundle\Validation\AbstractConstraintNormalizer;
use ITE\FormBundle\Validation\NormConstraint;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\AbstractComparison;

/**
 * Class AbstractComparisonNormalizer
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class AbstractComparisonNormalizer extends AbstractConstraintNormalizer
{
    /**
     * {@inheritdoc}
     */
    public function supports(Constraint $constraint)
    {
        return $constraint instanceof AbstractComparison;
    }

    /**
     * {@inheritdoc}
     */
    public function normalize(Constraint $constraint)
    {
        return new NormConstraint(
            $this->getType($constraint),
            $this->getMessage($constraint),
            $this->getOptions($constraint)
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function getMessage(Constraint $constraint)
    {
        /** @var $constraint AbstractComparison */
        return $this->translate($constraint->message, [
            '{{ value }}' => $this->formatValue($constraint->value, self::OBJECT_TO_STRING | self::PRETTY_DATE),
            '{{ compared_value }}' => $this->formatValue($constraint->value, self::OBJECT_TO_STRING | self::PRETTY_DATE),
            '{{ compared_value_type }}' => $this->formatTypeOf($constraint->value)
        ]);
    }

    /**
     * {@inheritdoc}
     */
    protected function getOptions(Constraint $constraint)
    {
        /** @var $constraint AbstractComparison */
        return [
            'value' => $constraint->value,
        ];
    }

}