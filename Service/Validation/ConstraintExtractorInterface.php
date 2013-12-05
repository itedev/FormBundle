<?php

namespace ITE\FormBundle\Service\Validation;

/**
 * Interface ConstraintExtractorInterface
 * @package ITE\FormBundle\Service\Validation
 */
interface ConstraintExtractorInterface
{
    /**
     * @param mixed $value
     * @return FormConstraint[]
     */
    public function getConstraints($value);
} 