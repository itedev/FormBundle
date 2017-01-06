<?php

namespace ITE\FormBundle\Validation\Mapping;

use ITE\FormBundle\Validation\ClientConstraint;
use Symfony\Component\Validator\Exception\ConstraintDefinitionException;

/**
 * Default implementation of {@link ClassMetadataInterface}.
 *
 * This class supports serialization and cloning.
 *
 * @author Bernhard Schussek <bschussek@gmail.com>
 * @author Fabien Potencier <fabien@symfony.com>
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class ClassMetadata extends GenericMetadata implements ClassMetadataInterface
{
    /**
     * @var string
     *
     * @internal This property is public in order to reduce the size of the
     *           class' serialized representation. Do not access it. Use
     *           {@link getClassName()} instead.
     */
    public $name;

    /**
     * @var string
     *
     * @internal This property is public in order to reduce the size of the
     *           class' serialized representation. Do not access it. Use
     *           {@link getDefaultGroup()} instead.
     */
    public $defaultGroup;

    /**
     * @var MemberMetadata[]
     *
     * @internal This property is public in order to reduce the size of the
     *           class' serialized representation. Do not access it. Use
     *           {@link getPropertyMetadata()} instead.
     */
    public $members = [];

    /**
     * @var PropertyMetadata[]
     *
     * @internal This property is public in order to reduce the size of the
     *           class' serialized representation. Do not access it. Use
     *           {@link getPropertyMetadata()} instead.
     */
    public $properties = [];

    /**
     * @var \ReflectionClass
     */
    private $reflClass;

    /**
     * Constructs a metadata for the given class.
     *
     * @param string $class
     */
    public function __construct($class)
    {
        $this->name = $class;
        // class name without namespace
        if (false !== $nsSep = strrpos($class, '\\')) {
            $this->defaultGroup = substr($class, $nsSep + 1);
        } else {
            $this->defaultGroup = $class;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function __sleep()
    {
        $parentProperties = parent::__sleep();

        return array_merge($parentProperties, [
            'members',
            'name',
            'properties',
            'defaultGroup',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getClassName()
    {
        return $this->name;
    }

    /**
     * Returns the name of the default group for this class.
     *
     * For each class, the group "Default" is an alias for the group
     * "<ClassName>", where <ClassName> is the non-namespaced name of the
     * class. All constraints implicitly or explicitly assigned to group
     * "Default" belong to both of these groups, unless the class defines
     * a group sequence.
     *
     * If a class defines a group sequence, validating the class in "Default"
     * will validate the group sequence. The constraints assigned to "Default"
     * can still be validated by validating the class in "<ClassName>".
     *
     * @return string The name of the default group
     */
    public function getDefaultGroup()
    {
        return $this->defaultGroup;
    }

    /**
     * {@inheritdoc}
     */
    public function addConstraint(ClientConstraint $constraint)
    {
        if (!in_array(ClientConstraint::CLASS_CONSTRAINT, (array) $constraint->getTargets())) {
            throw new ConstraintDefinitionException(sprintf(
                'The constraint "%s" cannot be put on classes.',
                get_class($constraint)
            ));
        }

        $constraint->addImplicitGroupName($this->getDefaultGroup());

        parent::addConstraint($constraint);

        return $this;
    }

    /**
     * Adds a constraint to the given property.
     *
     * @param string     $property   The name of the property
     * @param ClientConstraint $constraint The constraint
     *
     * @return ClassMetadata This object
     */
    public function addPropertyConstraint($property, ClientConstraint $constraint)
    {
        if (!isset($this->properties[$property])) {
            $this->properties[$property] = new PropertyMetadata($this->getClassName(), $property);

            $this->addPropertyMetadata($this->properties[$property]);
        }

        $constraint->addImplicitGroupName($this->getDefaultGroup());

        $this->properties[$property]->addConstraint($constraint);

        return $this;
    }

    /**
     * @param string       $property
     * @param ClientConstraint[] $constraints
     *
     * @return ClassMetadata
     */
    public function addPropertyConstraints($property, array $constraints)
    {
        foreach ($constraints as $constraint) {
            $this->addPropertyConstraint($property, $constraint);
        }

        return $this;
    }

    /**
     * Merges the constraints of the given metadata into this object.
     *
     * @param ClassMetadata $source The source metadata
     */
    public function mergeConstraints(ClassMetadata $source)
    {
        foreach ($source->getConstraints() as $constraint) {
            $this->addConstraint(clone $constraint);
        }

        foreach ($source->getConstrainedProperties() as $property) {
            foreach ($source->getPropertyMetadata($property) as $member) {
                $member = clone $member;

                foreach ($member->getConstraints() as $constraint) {
                    $constraint->addImplicitGroupName($this->getDefaultGroup());
                }

                $this->addPropertyMetadata($member);

                if ($member instanceof MemberMetadata && !$member->isPrivate($this->name)) {
                    $property = $member->getPropertyName();

                    if ($member instanceof PropertyMetadata && !isset($this->properties[$property])) {
                        $this->properties[$property] = $member;
                    }
                }
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function hasPropertyMetadata($property)
    {
        return array_key_exists($property, $this->members);
    }

    /**
     * {@inheritdoc}
     */
    public function getPropertyMetadata($property)
    {
        if (!isset($this->members[$property])) {
            return [];
        }

        return $this->members[$property];
    }

    /**
     * {@inheritdoc}
     */
    public function getConstrainedProperties()
    {
        return array_keys($this->members);
    }

    /**
     * Returns a ReflectionClass instance for this class.
     *
     * @return \ReflectionClass
     */
    public function getReflectionClass()
    {
        if (!$this->reflClass) {
            $this->reflClass = new \ReflectionClass($this->getClassName());
        }

        return $this->reflClass;
    }

    /**
     * Adds a property metadata.
     *
     * @param PropertyMetadataInterface $metadata
     */
    private function addPropertyMetadata(PropertyMetadataInterface $metadata)
    {
        $property = $metadata->getPropertyName();

        $this->members[$property][] = $metadata;
    }
}
