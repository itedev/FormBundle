<?php

namespace ITE\FormBundle\Service\Validation\Constraints;

use ITE\FormBundle\Service\Validation\ConstraintConverter;
use Symfony\Component\Validator\Constraints\Regex;

/**
 * Class RegexConverter
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class RegexConverter extends ConstraintConverter
{
    /** @var $constraint Regex */
    protected $constraint;

    /**
     * @return array
     */
    public function getOptions()
    {
        return array(
            'pattern' => $this->constraint->pattern,
            'htmlPattern' => $this->constraint->htmlPattern,
            'match' => $this->constraint->match,
        );
    }

} 