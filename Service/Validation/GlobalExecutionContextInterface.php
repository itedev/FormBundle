<?php

namespace ITE\FormBundle\Service\Validation;

use Symfony\Component\Validator\GlobalExecutionContextInterface as BaseGlobalExecutionContextInterface;

/**
 * Interface GlobalExecutionContextInterface
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
interface GlobalExecutionContextInterface extends BaseGlobalExecutionContextInterface
{
    /**
     * @return ConstraintMetadataFactory
     */
    public function getConstraintMetadataFactory();

    /**
     * Get constraints
     *
     * @return FormConstraint[]
     */
    public function getConstraints();

    /**
     * @param FormConstraint $constraint
     */
    public function addConstraint(FormConstraint $constraint);
} 