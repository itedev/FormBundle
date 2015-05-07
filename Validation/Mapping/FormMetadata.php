<?php

namespace ITE\FormBundle\Validation\Mapping;

use Symfony\Component\Validator\Constraint as ServerConstraint;
use ITE\FormBundle\Validation\Constraint as ClientConstraint;

/**
 * Class FormMetadata
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class FormMetadata implements FormMetadataInterface
{
    /**
     * @var array|ServerConstraint[] $serverConstraints
     */
    private $serverConstraints = [];

    /**
     * @var array|ServerConstraint[][] $serverConstraintsByGroup
     */
    private $serverConstraintsByGroup = [];

    /**
     * @var array|ClientConstraint[] $clientConstraints
     */
    private $clientConstraints = [];

    /**
     * @var array|ClientConstraint[][] $clientConstraintsByGroup
     */
    private $clientConstraintsByGroup = [];

    /**
     * @var  $parent
     */
    private $parent;

    /**
     * @var array|FormMetadata[] $children
     */
    private $children = [];

    /**
     * {@inheritdoc}
     */
    public function addServerConstraint(ServerConstraint $constraint)
    {
        $this->serverConstraints[] = $constraint;

        foreach ($constraint->groups as $group) {
            $this->serverConstraintsByGroup[$group][] = $constraint;
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function addServerConstraints(array $constraints)
    {
        foreach ($constraints as $constraint) {
            $this->addServerConstraint($constraint);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getServerConstraints()
    {
        return $this->serverConstraints;
    }

    /**
     * {@inheritdoc}
     */
    public function hasServerConstraints()
    {
        return count($this->serverConstraints) > 0;
    }

    /**
     * {@inheritdoc}
     */
    public function findServerConstraints($group)
    {
        return isset($this->serverConstraintsByGroup[$group])
            ? $this->serverConstraintsByGroup[$group]
            : [];
    }

    /**
     * {@inheritdoc}
     */
    public function addClientConstraint(ClientConstraint $constraint)
    {
        $this->clientConstraints[] = $constraint;

        foreach ($constraint->groups as $group) {
            $this->clientConstraintsByGroup[$group][] = $constraint;
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function addClientConstraints(array $constraints)
    {
        foreach ($constraints as $constraint) {
            $this->addClientConstraint($constraint);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getClientConstraints()
    {
        return $this->serverConstraints;
    }

    /**
     * {@inheritdoc}
     */
    public function hasClientConstraints()
    {
        return count($this->serverConstraints) > 0;
    }

    /**
     * {@inheritdoc}
     */
    public function findClientConstraints($group)
    {
        return isset($this->serverConstraintsByGroup[$group])
            ? $this->serverConstraintsByGroup[$group]
            : [];
    }

    /**
     * {@inheritdoc}
     */
    public function isRoot()
    {
        return null === $this->parent;
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * {@inheritdoc}
     */
    public function setParent(FormMetadata $parent = null)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function addChild($name, FormMetadata $formMetadata)
    {
        $this->children[$name] = $formMetadata;
        $formMetadata->setParent($this);

        return $this;
    }
}