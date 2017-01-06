<?php

namespace ITE\FormBundle\Validation\Mapping;

use Symfony\Component\Validator\Constraint;
use ITE\FormBundle\Validation\ClientConstraint;

/**
 * Interface FormMetadata
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
interface FormMetadataInterface
{
    /**
     * @param ClientConstraint $constraint
     * @return $this
     */
    public function addConstraint(ClientConstraint $constraint);

    /**
     * @param array $constraints
     * @return $this
     */
    public function addConstraints(array $constraints);

    /**
     * @return array
     */
    public function getConstraints();

    /**
     * @return bool
     */
    public function hasConstraints();

    /**
     * @param $group
     * @return array
     */
    public function findConstraints($group);
}
