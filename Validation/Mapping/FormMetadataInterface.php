<?php

namespace ITE\FormBundle\Validation\Mapping;

use Symfony\Component\Validator\Constraint as ServerConstraint;
use ITE\FormBundle\Validation\Constraint as ClientConstraint;

/**
 * Interface FormMetadata
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
interface FormMetadataInterface
{
    /**
     * @param ServerConstraint $constraint
     * @return $this
     */
    public function addServerConstraint(ServerConstraint $constraint);

    /**
     * @param array $constraints
     * @return $this
     */
    public function addServerConstraints(array $constraints);

    /**
     * @return array
     */
    public function getServerConstraints();

    /**
     * @return bool
     */
    public function hasServerConstraints();

    /**
     * @param $group
     * @return array
     */
    public function findServerConstraints($group);

    /**
     * @param ClientConstraint $constraint
     * @return $this
     */
    public function addClientConstraint(ClientConstraint $constraint);

    /**
     * @param array $constraints
     * @return $this
     */
    public function addClientConstraints(array $constraints);

    /**
     * @return array
     */
    public function getClientConstraints();

    /**
     * @return bool
     */
    public function hasClientConstraints();

    /**
     * @param $group
     * @return array
     */
    public function findClientConstraints($group);

    /**
     * @return bool
     */
    public function isRoot();

    /**
     * Get parent
     *
     * @return FormMetadata|null
     */
    public function getParent();

    /**
     * Set parent
     *
     * @param FormMetadata|null $parent
     * @return FormMetadata
     */
    public function setParent(FormMetadata $parent = null);

    /**
     * @param $name
     * @param FormMetadata $formMetadata
     * @return $this
     */
    public function addChild($name, FormMetadata $formMetadata);
}