<?php

namespace ITE\FormBundle\Validation\Mapping;

use ITE\FormBundle\Validation\Constraint;

/**
 * A generic container of {@link Constraint} objects.
 *
 * This class supports serialization and cloning.
 *
 * @since  2.5
 * @author Bernhard Schussek <bschussek@gmail.com>
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class GenericMetadata implements MetadataInterface
{
    /**
     * @var Constraint[]
     *
     * @internal This property is public in order to reduce the size of the
     *           class' serialized representation. Do not access it. Use
     *           {@link getConstraints()} and {@link findConstraints()} instead.
     */
    public $constraints = array();

    /**
     * @var array
     *
     * @internal This property is public in order to reduce the size of the
     *           class' serialized representation. Do not access it. Use
     *           {@link findConstraints()} instead.
     */
    public $constraintsByGroup = array();

    /**
     * Returns the names of the properties that should be serialized.
     *
     * @return string[]
     */
    public function __sleep()
    {
        return array(
            'constraints',
            'constraintsByGroup',
        );
    }

    /**
     * Clones this object.
     */
    public function __clone()
    {
        $constraints = $this->constraints;

        $this->constraints = array();
        $this->constraintsByGroup = array();

        foreach ($constraints as $constraint) {
            $this->addConstraint(clone $constraint);
        }
    }

    /**
     * Adds a constraint.
     *
     * @param Constraint $constraint The constraint to add
     *
     * @return GenericMetadata This object
     */
    public function addConstraint(Constraint $constraint)
    {
        $this->constraints[] = $constraint;

        foreach ($constraint->groups as $group) {
            $this->constraintsByGroup[$group][] = $constraint;
        }

        return $this;
    }

    /**
     * Adds an list of constraints.
     *
     * @param Constraint[] $constraints The constraints to add
     *
     * @return GenericMetadata This object
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
     * Returns whether this element has any constraints.
     *
     * @return bool
     */
    public function hasConstraints()
    {
        return count($this->constraints) > 0;
    }

    /**
     * {@inheritdoc}
     *
     * Aware of the global group (* group).
     */
    public function findConstraints($group)
    {
        return isset($this->constraintsByGroup[$group])
            ? $this->constraintsByGroup[$group]
            : array();
    }
}
