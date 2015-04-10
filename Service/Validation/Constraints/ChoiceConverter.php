<?php

namespace ITE\FormBundle\Service\Validation\Constraints;

use ITE\FormBundle\Service\Validation\ConstraintConverter;
use ITE\FormBundle\Service\Validation\ConstraintMetadataInterface;
use Symfony\Component\Validator\Constraints\Choice;

/**
 * Class ChoiceConverter
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class ChoiceConverter extends ConstraintConverter
{
    /** @var $constraint Choice */
    protected $constraint;

    /**
     * @return string
     */
    public function getType()
    {
        if ($this->constraint->multiple) {
            $type = ConstraintMetadataInterface::TYPE_CHOICE_MULTIPLE;
        } else {
            $type = ConstraintMetadataInterface::TYPE_CHOICE_SINGLE;
        }

        return $type;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        if ($this->constraint->multiple) {
            $message = $this->translate($this->constraint->multipleMessage);
//            if ($this->constraint->min !== null && $this->constraint->max !== null) {
//                $message = $this->translate('This value should be between {{ min }} and {{ max }}.', array(
//                    '{{ min }}' => $this->constraint->min,
//                    '{{ max }}' => $this->constraint->max,
//                ), (int) $this->constraint->min);
//            } elseif ($this->constraint->min !== null) {
//                $message = $this->translate($this->constraint->minMessage, array(
//                    '{{ limit }}' => $this->constraint->min
//                ), (int) $this->constraint->min);
//            } elseif ($this->constraint->max !== null) {
//                $message = $this->translate($this->constraint->maxMessage, array(
//                    '{{ limit }}' => $this->constraint->max
//                ), (int) $this->constraint->max);
//            }
        } else {
            $message = $this->translate($this->constraint->message);
        }

        return $message;
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return array(
            'choices' => $this->constraint->choices,
            'multiple' => $this->constraint->multiple,
            'strict' => $this->constraint->strict,
            'min' => $this->constraint->min,
            'max' => $this->constraint->max,
        );
    }

} 