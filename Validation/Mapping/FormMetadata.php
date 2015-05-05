<?php

namespace ITE\FormBundle\Validation\Mapping;

use Symfony\Component\Validator\Constraint;

/**
 * Class FormMetadata
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class FormMetadata
{
    /**
     * @var array $constraints
     */
    private $constraints = [];

    /**
     * @var array $constraintsByGroup
     */
    private $constraintsByGroup = [];

    /**
     * @var array|FormMetadata[] $children
     */
    private $children = [];

    /**
     * @param Constraint $constraint
     * @return $this
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
     * @param array $constraints
     * @return $this
     */
    public function addConstraints(array $constraints)
    {
        foreach ($constraints as $constraint) {
            $this->addConstraint($constraint);
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getConstraints()
    {
        return $this->constraints;
    }

    /**
     * @return bool
     */
    public function hasConstraints()
    {
        return count($this->constraints) > 0;
    }

    /**
     * @param $group
     * @return array
     */
    public function findConstraints($group)
    {
        return isset($this->constraintsByGroup[$group])
            ? $this->constraintsByGroup[$group]
            : [];
    }

    /**
     * @param $name
     * @param FormMetadata $formMetadata
     * @return $this
     */
    public function add($name, FormMetadata $formMetadata)
    {
        $this->children[$name] = $formMetadata;

        return $this;
    }
}