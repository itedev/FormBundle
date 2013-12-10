<?php

namespace ITE\FormBundle\Service\Validation\Constraints;

use ITE\FormBundle\Service\Validation\ConstraintConverter;
use ITE\FormBundle\Service\Validation\ConstraintMetadataInterface;
use Symfony\Component\Validator\Constraints\Count;

/**
 * Class CountConverter
 * @package ITE\FormBundle\Service\Validation\Constraints
 */
class CountConverter extends ConstraintConverter
{
    /** @var $constraint Count */
    protected $constraint;

    /**
     * @return string
     */
    public function getType()
    {
        if ($this->constraint->min == $this->constraint->max) {
            $type = ConstraintMetadataInterface::TYPE_COUNT_EQUAL_TO;
        } elseif (null !== $this->constraint->max && null !== $this->constraint->min) {
            $type = ConstraintMetadataInterface::TYPE_COUNT_RANGE;
        } elseif (null !== $this->constraint->max) {
            $type = ConstraintMetadataInterface::TYPE_COUNT_LESS_THAN_OR_EQUAL;
        } else {
            $type = ConstraintMetadataInterface::TYPE_COUNT_GREATER_THAN_OR_EQUAL;
        }

        return $type;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        if ($this->constraint->min == $this->constraint->max) {
            $message = $this->translate($this->constraint->exactMessage, array(
                '{{ limit }}' => $this->constraint->min,
            ), (int) $this->constraint->min);
        } elseif (null !== $this->constraint->max && null !== $this->constraint->min) {
            $message = $this->translate('This collection should contain between {{ min }} and {{ max }} elements.', array(
                '{{ min }}' => $this->constraint->min,
                '{{ max }}' => $this->constraint->min,
            ), (int) $this->constraint->min);
        } elseif (null !== $this->constraint->max) {
            $message = $this->translate($this->constraint->maxMessage, array(
                '{{ limit }}' => $this->constraint->max,
            ), (int) $this->constraint->max);
        } else {
            $message = $this->translate($this->constraint->minMessage, array(
                '{{ limit }}' => $this->constraint->min,
            ), (int) $this->constraint->min);
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