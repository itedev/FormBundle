<?php

namespace ITE\FormBundle\Service\Validation\Constraints;

use ITE\FormBundle\Service\Validation\ConstraintConverter;
use Symfony\Component\Validator\Constraints\Email;

/**
 * Class EmailConverter
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class EmailConverter extends ConstraintConverter
{
    /** @var $constraint Email */
    protected $constraint;

    /**
     * @return array
     */
    public function getOptions()
    {
        return array(
            'checkMX' => $this->constraint->checkMX,
            'checkHost' => $this->constraint->checkHost,
        );
    }

} 