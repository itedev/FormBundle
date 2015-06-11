<?php

namespace ITE\FormBundle\Validation\ConstraintTransformer\JQueryValidation;

use ITE\FormBundle\Validation\ClientConstraint;
use ITE\FormBundle\Validation\ConstraintTransformerInterface;
use ITE\FormBundle\Validation\Constraints as ClientAssert;

/**
 * Class DefaultTransformer
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class DefaultTransformer implements ConstraintTransformerInterface
{
    /**
     * {@inheritdoc}
     */
    public function transform(ClientConstraint $constraint)
    {
        $class = get_class($constraint);

        $result = [];
        if ($constraint instanceof ClientAssert\Range) {
            if (null !== $constraint->min && null !== $constraint->max) {
                $result['range'] = [$constraint->min, $constraint->max];
            } elseif (null !== $constraint->min) {

            } else {

            }
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function supports(ClientConstraint $constraint)
    {
        return in_array(get_class($constraint), [
            'ITE\FormBundle\Validation\Constraints\Range',
        ]);
    }

}