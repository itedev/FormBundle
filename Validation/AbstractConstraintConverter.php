<?php

namespace ITE\FormBundle\Validation;

use Symfony\Component\Validator\Constraint;

/**
 * Class AbstractConstraintConverter
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
abstract class AbstractConstraintConverter implements ConstraintConverterInterface
{
    /**
     * {@inheritdoc}
     */
    abstract public function supports(Constraint $constraint);

    /**
     * {@inheritdoc}
     */
    abstract public function convert(Constraint $constraint);

    /**
     * @param Constraint $constraint
     * @param string $clientConstraintClass
     * @return array
     */
    protected function getOptions(Constraint $constraint, $clientConstraintClass)
    {
        $options = [];

        $constraintOptions = get_object_vars($constraint);
        foreach ($constraintOptions as $name => $value) {
            if (!property_exists($clientConstraintClass, $name)) {
                continue;
            }

            $options[$name] = $value;
        }

        return $options;
    }
}