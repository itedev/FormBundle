<?php

namespace ITE\FormBundle\Service\Validation\Constraints;

use ITE\FormBundle\Service\Validation\ConstraintConverter;
use ITE\FormBundle\Service\Validation\ConstraintMetadataInterface;
use Symfony\Component\Validator\Constraints\Length;

/**
 * Class LengthConverter
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class LengthConverter extends ConstraintConverter
{
    /** @var $constraint Length */
    protected $constraint;

    /**
     * @return string
     */
    public function getType()
    {
        if ($this->constraint->min == $this->constraint->max) {
            $type = ConstraintMetadataInterface::TYPE_LENGTH_EQUAL_TO;
        } elseif (null !== $this->constraint->max && null !== $this->constraint->min) {
            $type = ConstraintMetadataInterface::TYPE_LENGTH_RANGE;
        } elseif (null !== $this->constraint->max) {
            $type = ConstraintMetadataInterface::TYPE_LENGTH_LESS_THAN_OR_EQUAL;
        } else {
            $type = ConstraintMetadataInterface::TYPE_LENGTH_GREATER_THAN_OR_EQUAL;
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
            $message = $this->translate('This value should be between {{ min }} and {{ max }} characters long.', array(
                '{{ min }}' => $this->constraint->min,
                '{{ max }}' => $this->constraint->max,
            ));
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