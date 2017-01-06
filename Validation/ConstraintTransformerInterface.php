<?php

namespace ITE\FormBundle\Validation;

/**
 * Interface ConstraintTransformerInterface
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
interface ConstraintTransformerInterface
{
    /**
     * @param ClientConstraint $constraint
     * @return array
     */
    public function transform(ClientConstraint $constraint);

    /**
     * @param ClientConstraint $constraint
     * @return bool
     */
    public function supports(ClientConstraint $constraint);
}
