<?php

namespace ITE\FormBundle\Service\Validation\Constraints;

use ITE\FormBundle\Service\Validation\ConstraintConverter;
use Symfony\Component\Validator\Constraints\CardScheme;

/**
 * Class CardSchemeConverter
 * @package ITE\FormBundle\Service\Validation\Constraints
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