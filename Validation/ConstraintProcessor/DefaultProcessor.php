<?php

namespace ITE\FormBundle\Validation\ConstraintProcessor;

use ITE\FormBundle\Validation\AbstractConstraintProcessor;
use ITE\FormBundle\Validation\Constraint;

/**
 * Class DefaultProcessor
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class DefaultProcessor extends AbstractConstraintProcessor
{
    /**
     * {@inheritdoc}
     */
    public function supports(Constraint $constraint)
    {
        return in_array(get_class($constraint), [
            'ITE\FormBundle\Validation\Constraints\Blank',
            'ITE\FormBundle\Validation\Constraints\CardScheme',
            'ITE\FormBundle\Validation\Constraints\Country',
            'ITE\FormBundle\Validation\Constraints\Currency',
            'ITE\FormBundle\Validation\Constraints\False',
            'ITE\FormBundle\Validation\Constraints\Language',
            'ITE\FormBundle\Validation\Constraints\Locale',
            'ITE\FormBundle\Validation\Constraints\NotBlank',
            'ITE\FormBundle\Validation\Constraints\True',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function process(Constraint $constraint)
    {
        $constraint->message = $this->translate($constraint->message);
    }
}