<?php

namespace ITE\FormBundle\Service\Validator;

use Symfony\Component\Validator\GlobalExecutionContextInterface as BaseGlobalExecutionContextInterface;

/**
 * Interface GlobalExecutionContextInterface
 * @package ITE\FormBundle\Service\Validator
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
     * @return array
     */
    public function getConstraints();

    /**
     * @param FieldConstraint $constraint
     */
    public function addConstraint(FieldConstraint $constraint);
} 