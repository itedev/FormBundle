<?php

namespace ITE\FormBundle\Form\DataTransformer;

use Doctrine\Common\Util\ClassUtils;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadata;
use Symfony\Bridge\Doctrine\Form\ChoiceList\EntityLoaderInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

/**
 * Class EntityToIdTransformer
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class EntityToIdTransformer implements DataTransformerInterface
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var string
     */
    private $class;

    /**
     * @var EntityLoaderInterface
     */
    private $loader;

    /**
     * @var bool
     */
    private $multiple;

    /**
     * @var ClassMetadata
     */
    private $classMetadata;

    /**
     * @var string
     */
    private $identifierFieldName;

    /**
     * @var string
     */
    private $separator;

    /**
     * @var bool
     */
    private $strict;

    /**
     * @param EntityManager $em
     * @param string $class
     * @param EntityLoaderInterface $loader
     * @param bool $multiple
     * @param string $separator
     */
    public function __construct(
        EntityManager $em,
        $class,
        EntityLoaderInterface $loader,
        $multiple,
        $separator,
        bool $strict = true
    ) {
        $this->em = $em;
        $this->loader = $loader;
        $this->multiple = $multiple;
        $this->separator = $separator;
        $this->strict = $strict;

        $this->classMetadata = $this->em->getClassMetadata($class);
        $this->class = $this->classMetadata->getName();
        $this->identifierFieldName = $this->classMetadata->getSingleIdentifierFieldName();
    }

    /**
     * {@inheritdoc}
     */
    public function transform($value)
    {
        if (null === $value) {
            return;
        }

        $ids = [];
        if ($this->multiple) {
            if (!is_array($value) && !$value instanceof \Traversable) {
                throw new TransformationFailedException('Expected an array.');
            }
            foreach ($value as $entity) {
                $ids[] = $this->getIdentifierValue($entity);
            }
        } else {
            $ids[] = $this->getIdentifierValue($value);
        }

        return implode($this->separator, $ids);
    }

    /**
     * {@inheritdoc}
     */
    public function reverseTransform($value)
    {
        if (null === $value || '' === $value) {
            return $this->multiple ? [] : null;
        }
        if (!is_string($value)) {
            throw new TransformationFailedException('Expected a string.');
        }

        $ids = explode($this->separator, $value);
        if (empty($ids)) {
            return $this->multiple ? [] : null;
        }

        $unorderedEntities = $this->loader->getEntitiesByIds($this->identifierFieldName, $ids);

        $entitiesById = [];
        $entities = [];
        foreach ($unorderedEntities as $entity) {
            $id = $this->getIdentifierValue($entity);
            $entitiesById[$id] = $entity;
        }
        foreach ($ids as $i => $id) {
            if (isset($entitiesById[$id])) {
                $entities[$i] = $entitiesById[$id];
            }
        }

        if (count($ids) !== count($entities) && $this->strict) {
            throw new TransformationFailedException('Could not find all matching entities for the given ids');
        }

        if (!$this->multiple) {
            return $entities[0] ?? null;
        } else {
            return $entities;
        }
    }

    /**
     * @param object $entity
     * @return string
     */
    private function getIdentifierValue($entity)
    {
//        if (!$this->em->contains($entity)) {
//            throw new TransformationFailedException(
//                'Entities passed to the choice field must be managed. Maybe '.
//                'persist them in the entity manager?'
//            );
//        }
//
//        $this->em->initializeObject($entity);


        $class = $this->class;

        if (!($entity instanceof $class)) {
            throw new TransformationFailedException(sprintf(
                'Expected instance of "%s", instance of "%s" given',
                $this->class,
                get_class($entity)
            ));
        }

        return (string) current($this->classMetadata->getIdentifierValues($entity));
    }
}
