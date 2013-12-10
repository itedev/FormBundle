<?php

namespace ITE\FormBundle\Service\Validation\Constraints;

use ITE\FormBundle\Service\Validation\ConstraintConverter;
use Symfony\Component\Validator\Constraints\Ip;

/**
 * Class IpConverter
 * @package ITE\FormBundle\Service\Validation\Constraints
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