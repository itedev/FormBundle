<?php

namespace ITE\FormBundle\Validation\Mapping;

use ITE\FormBundle\Validation\ClientConstraint;
use Symfony\Component\Validator\Exception\ConstraintDefinitionException;

/**
 * Stores all metadata needed for validating a class property.
 *
 * The method of accessing the property's value must be specified by subclasses
 * by implementing the {@link newReflectionMember()} method.
 *
 * This class supports serialization and cloning.
 *
 * @author Bernhard Schussek <bschussek@gmail.com>
 * @author c1tru55 <mr.c1tru55@gmail.com>
 *
 * @see PropertyMetadataInterface
 */
abstract class MemberMetadata extends GenericMetadata implements PropertyMetadataInterface
{
    /**
     * @var string
     *
     * @internal This property is public in order to reduce the size of the
     *           class' serialized representation. Do not access it. Use
     *           {@link getClassName()} instead.
     */
    public $class;

    /**
     * @var string
     *
     * @internal This property is public in order to reduce the size of the
     *           class' serialized representation. Do not access it. Use
     *           {@link getName()} instead.
     */
    public $name;

    /**
     * @var string
     *
     * @internal This property is public in order to reduce the size of the
     *           class' serialized representation. Do not access it. Use
     *           {@link getPropertyName()} instead.
     */
    public $property;

    /**
     * @var \ReflectionMethod[]|\ReflectionProperty[]
     */
    private $reflMember = [];

    /**
     * Constructor.
     *
     * @param string $class    The name of the class this member is defined on
     * @param string $name     The name of the member
     * @param string $property The property the member belongs to
     */
    public function __construct($class, $name, $property)
    {
        $this->class = $class;
        $this->name = $name;
        $this->property = $property;
    }

    /**
     * {@inheritdoc}
     */
    public function addConstraint(ClientConstraint $constraint)
    {
        if (!in_array(ClientConstraint::PROPERTY_CONSTRAINT, (array) $constraint->getTargets())) {
            throw new ConstraintDefinitionException(sprintf(
                'The constraint %s cannot be put on properties or getters',
                get_class($constraint)
            ));
        }

        parent::addConstraint($constraint);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function __sleep()
    {
        return array_merge(parent::__sleep(), [
            'class',
            'name',
            'property',
        ]);
    }

    /**
     * Returns the name of the member.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function getClassName()
    {
        return $this->class;
    }

    /**
     * {@inheritdoc}
     */
    public function getPropertyName()
    {
        return $this->property;
    }

    /**
     * Returns whether this member is public.
     *
     * @param object|string $objectOrClassName The object or the class name
     *
     * @return bool
     */
    public function isPublic($objectOrClassName)
    {
        return $this->getReflectionMember($objectOrClassName)->isPublic();
    }

    /**
     * Returns whether this member is protected.
     *
     * @param object|string $objectOrClassName The object or the class name
     *
     * @return bool
     */
    public function isProtected($objectOrClassName)
    {
        return $this->getReflectionMember($objectOrClassName)->isProtected();
    }

    /**
     * Returns whether this member is private.
     *
     * @param object|string $objectOrClassName The object or the class name
     *
     * @return bool
     */
    public function isPrivate($objectOrClassName)
    {
        return $this->getReflectionMember($objectOrClassName)->isPrivate();
    }

    /**
     * Returns the reflection instance for accessing the member's value.
     *
     * @param object|string $objectOrClassName The object or the class name
     *
     * @return \ReflectionMethod|\ReflectionProperty The reflection instance
     */
    public function getReflectionMember($objectOrClassName)
    {
        $className = is_string($objectOrClassName) ? $objectOrClassName : get_class($objectOrClassName);
        if (!isset($this->reflMember[$className])) {
            $this->reflMember[$className] = $this->newReflectionMember($objectOrClassName);
        }

        return $this->reflMember[$className];
    }

    /**
     * Creates a new reflection instance for accessing the member's value.
     *
     * Must be implemented by subclasses.
     *
     * @param object|string $objectOrClassName The object or the class name
     *
     * @return \ReflectionMethod|\ReflectionProperty The reflection instance
     */
    abstract protected function newReflectionMember($objectOrClassName);
}
