<?php

namespace ITE\FormBundle\Validation;

use Symfony\Component\Validator\Constraint;
use ITE\FormBundle\Validation\Constraint as ClientConstraint;

/**
 * Interface ConstraintConverterInterface
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
interface ConstraintConverterInterface
{
    /**
     * @param Constraint $constraint
     * @return bool
     */
    public function supports(Constraint $constraint);

    /**
     * @param Constraint $constraint
     * @return ClientConstraint
     */
    public function convert(Constraint $constraint);
}