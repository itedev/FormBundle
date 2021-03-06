<?php

namespace ITE\FormBundle\Service\Validation\Constraints;

use ITE\FormBundle\Service\Validation\ConstraintConverter;
use Symfony\Component\Validator\Constraints\Type;

/**
 * Class TypeConverter
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class TypeConverter extends ConstraintConverter
{
    /** @var $constraint Type */
    protected $constraint;

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->translate($this->constraint->message, array(
            '{{ type }}' => $this->constraint->type,
        ));
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return array(
            'type' => $this->constraint->type
        );
    }

}