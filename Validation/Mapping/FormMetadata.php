<?php

namespace ITE\FormBundle\Validation\Mapping;

use ITE\FormBundle\Validation\ClientConstraint;

/**
 * Class FormMetadata
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class FormMetadata implements FormMetadataInterface
{
    /**
     * @var array|ClientConstraint[] $constraints
     */
    private $constraints = [];

    /**
     * @var array|ClientConstraint[][] $constraintsByGroup
     */
    private $constraintsByGroup = [];

    /**
     * {@inheritdoc}
     */
    public function addConstraint(ClientConstraint $constraint)
    {
        $this->constraints[] = $constraint;

        foreach ($constraint->groups as $group) {
            $this->constraintsByGroup[$group][] = $constraint;
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function addConstraints(array $constraints)
    {
        foreach ($constraints as $constraint) {
            $this->addConstraint($constraint);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getConstraints()
    {
        return $this->constraints;
    }

    /**
     * {@inheritdoc}
     */
    public function hasConstraints()
    {
        return count($this->constraints) > 0;
    }

    /**
     * {@inheritdoc}
     */
    public function findConstraints($group)
    {
        return isset($this->constraintsByGroup[$group])
            ? $this->constraintsByGroup[$group]
            : [];
    }
}
