<?php

namespace ITE\FormBundle\Validation;

/**
 * Interface ConstraintProcessorInterface
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
interface ConstraintProcessorInterface
{
    /**
     * @param ClientConstraint $constraint
     * @return bool
     */
    public function supports(ClientConstraint $constraint);

    /**
     * @param ClientConstraint $constraint
     */
    public function process(ClientConstraint $constraint);
}