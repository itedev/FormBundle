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
     * @param Constraint $constraint
     * @return bool
     */
    public function supports(Constraint $constraint);

    /**
     * @param Constraint $constraint
     */
    public function process(Constraint $constraint);
}