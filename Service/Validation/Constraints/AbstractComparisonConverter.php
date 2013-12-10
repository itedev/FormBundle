<?php

namespace ITE\FormBundle\Service\Validation\Constraints;

use ITE\FormBundle\Service\Validation\ConstraintConverter;
use Symfony\Component\Validator\Constraints\AbstractComparison;

/**
 * Class AbstractComparisonConverter
 * @package ITE\FormBundle\Service\Validation\Constraints
 */
class AbstractComparisonConverter extends ConstraintConverter
{
    /** @var $constraint AbstractComparison */
    protected $constraint;

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->translate($this->constraint->message, array(
            '{{ value }}' => $this->valueToString($this->constraint->value),
            '{{ compared_value }}' => $this->valueToString($this->constraint->value),
            '{{ compared_value_type }}' => $this->valueToType($this->constraint->value)
        ));
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return array(
            'value' => $this->constraint->value,
        );
    }

    /**
     * Returns a string representation of the type of the value.
     *
     * @param  mixed $value
     *
     * @return string
     */
    protected function valueToType($value)
    {
        return is_object($value) ? get_class($value) : gettype($value);
    }

    /**
     * Returns a string representation of the value.
     *
     * @param  mixed  $value
     *
     * @return string
     */
    protected function valueToString($value)
    {
        if ($value instanceof \DateTime) {
            return $value->format('Y-m-d H:i:s');
        }

        return var_export($value, true);
    }
} 