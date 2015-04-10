<?php

namespace ITE\FormBundle\Service\Validation;

/**
 * Interface ConstraintExtractorInterface
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
interface ConstraintExtractorInterface
{
    /**
     * @param mixed $value
     * @return FormConstraint[]
     */
    public function getConstraints($value);
} 