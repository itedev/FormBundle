<?php

namespace ITE\FormBundle\Service\Validation\Constraints;

use ITE\FormBundle\Service\Validation\ConstraintConverter;
use ITE\FormBundle\Service\Validation\ConstraintMetadataInterface;
use Symfony\Component\Validator\Constraints\Range;

/**
 * Class RangeConverter
 * @package ITE\FormBundle\Service\Validation\Constraints
 */
class RangeConverter extends ConstraintConverter
{
    /** @var $constraint Range */
    protected $constraint;

    /**
     * @return string
     */
    public function getType()
    {
        if (null !== $this->constraint->max && null !== $this->constraint->min) {
            $type = ConstraintMetadataInterface::TYPE_RANGE;
        } elseif (null !== $this->constraint->max) {
            $type = ConstraintMetadataInterface::TYPE_RANGE_LESS_THAN_OR_EQUAL;
        } else {
            $type = ConstraintMetadataInterface::TYPE_RANGE_GREATER_THAN_OR_EQUAL;
        }

        return $type;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        if (null !== $this->constraint->max && null !== $this->constraint->min) {
            $message = $this->translate('This value should be between {{ min }} and {{ max }}.', array(
                '{{ min }}' => $this->constraint->min,
                '{{ max }}' => $this->constraint->max,
            ));
        } elseif (null !== $this->constraint->max) {
            $message = $this->translate($this->constraint->maxMessage, array(
                '{{ limit }}' => $this->constraint->max,
            ));
        } else {
            $message = $this->translate($this->constraint->minMessage, array(
                '{{ limit }}' => $this->constraint->min,
            ));
        }

        return $message;
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return array(
            'min' => $this->constraint->min,
            'max' => $this->constraint->max,
        );
    }

} 