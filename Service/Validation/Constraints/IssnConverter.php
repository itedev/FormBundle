<?php

namespace ITE\FormBundle\Service\Validation\Constraints;

use ITE\FormBundle\Service\Validation\ConstraintConverter;
use Symfony\Component\Validator\Constraints\Issn;

/**
 * Class IssnConverter
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class IssnConverter extends ConstraintConverter
{
    /** @var $constraint Issn */
    protected $constraint;

    /**
     * @return array
     */
    public function getOptions()
    {
        return array(
            'caseSensitive' => $this->constraint->caseSensitive,
            'requireHyphen' => $this->constraint->requireHyphen,
        );
    }

} 