<?php

namespace ITE\FormBundle\Validation\ConstraintConverter;

use ITE\FormBundle\Validation\AbstractConstraintConverter;
use Symfony\Component\Validator\Constraint;

/**
 * Class DefaultConverter
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class DefaultConverter extends AbstractConstraintConverter
{
    /**
     * {@inheritdoc}
     */
    public function supports(Constraint $constraint)
    {
        return in_array(get_class($constraint), [
            'Symfony\Component\Validator\Constraints\Blank',
            'Symfony\Component\Validator\Constraints\CardScheme',
            'Symfony\Component\Validator\Constraints\Count',
            'Symfony\Component\Validator\Constraints\Country',
            'Symfony\Component\Validator\Constraints\Currency',
            'Symfony\Component\Validator\Constraints\EqualTo',
            'Symfony\Component\Validator\Constraints\False',
            'Symfony\Component\Validator\Constraints\GreaterThan',
            'Symfony\Component\Validator\Constraints\GreaterThanOrEqual',
            'Symfony\Component\Validator\Constraints\Language',
            'Symfony\Component\Validator\Constraints\Length',
            'Symfony\Component\Validator\Constraints\LessThan',
            'Symfony\Component\Validator\Constraints\LessThanOrEqual',
            'Symfony\Component\Validator\Constraints\Locale',
            'Symfony\Component\Validator\Constraints\NotBlank',
            'Symfony\Component\Validator\Constraints\NotEqualTo',
            'Symfony\Component\Validator\Constraints\Range',
            'Symfony\Component\Validator\Constraints\True',
            'Symfony\Component\Validator\Constraints\Type',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function convert(Constraint $constraint)
    {
        $class = get_class($constraint);
        $className = substr($class, strrpos($class, '\\') + 1);

        $newClass = 'ITE\\FormBundle\\Validation\\Constraints\\' . $className;
        $options = $this->getOptions($constraint, $newClass);

        return new $newClass($options);
    }
}
