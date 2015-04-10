<?php

namespace ITE\FormBundle\Service\Validation\Constraints;

use ITE\FormBundle\Service\Validation\ConstraintConverter;
use Symfony\Component\Validator\Constraints\Url;

/**
 * Class UrlConverter
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class UrlConverter extends ConstraintConverter
{
    /** @var $constraint Url */
    protected $constraint;

    /**
     * @return array
     */
    public function getOptions()
    {
        return array(
            'protocols' => $this->constraint->protocols,
        );
    }

} 