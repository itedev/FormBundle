<?php

namespace ITE\FormBundle\Validation;

use Symfony\Component\Validator\Constraint as ServerConstraint;

/**
 * Interface ConstraintConverterInterface
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
interface ConstraintConverterInterface
{
    /**
     * @param ServerConstraint $constraint
     * @return bool
     */
    public function supports(ServerConstraint $constraint);

    /**
     * @param ServerConstraint $constraint
     * @return Constraint
     */
    public function convert(ServerConstraint $constraint);
}