<?php

namespace ITE\FormBundle\Service\Validation\Constraints;

use ITE\FormBundle\Service\Validation\ConstraintConverter;
use Symfony\Component\Validator\Constraints\Ip;

/**
 * Class IpConverter
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class IpConverter extends ConstraintConverter
{
    /** @var $constraint Ip */
    protected $constraint;

    /**
     * @return array
     */
    public function getOptions()
    {
        return array(
            'version' => $this->constraint->version,
        );
    }

} 