<?php

namespace ITE\FormBundle\Service\Validation\Constraints;

use ITE\FormBundle\Service\Validation\ConstraintConverter;
use Symfony\Component\Validator\Constraints\CardScheme;

/**
 * Class CardSchemeConverter
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class CardSchemeConverter extends ConstraintConverter
{
    /** @var $constraint CardScheme */
    protected $constraint;

    /**
     * @return array
     */
    public function getOptions()
    {
        return array(
            'schemes' => $this->constraint->schemes,
        );
    }

} 