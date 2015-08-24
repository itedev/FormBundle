<?php

namespace ITE\FormBundle\Form\DataTransformer;

use Doctrine\Common\Util\ClassUtils;
use Doctrine\ORM\EntityManager;
use ITE\FormBundle\Util\MixedEntityUtils;
use Symfony\Bridge\Doctrine\Form\ChoiceList\EntityLoaderInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

/**
 * Class EntityToIdTransformer
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class MixedEntityToIdTransformer implements DataTransformerInterface
{
    /**
     * @var array
     */
    private $options;

    /**
     * @var bool
     */
    private $multiple;

    /**
     * @var string
     */
    private $separator;

    /**
     * @var array
     */
    private $aliases;

    /**
     * @param array $options
     * @param bool $multiple
     * @param string $separator
     */
    public function __construct($options, $multiple, $separator)
    {
        foreach ($options as $alias => $entityOptions) {
            /** @var EntityManager $em */
            $em = $entityOptions['em'];
            $class = $entityOptions['class'];

            $classMetadata = $em->getClassMetadata($class);
            $class = $classMetadata->getName();
            $identifierFieldName = $classMetadata->getSingleIdentifierFieldName();

            $options[$alias]['class'] = $class;
            $options[$alias]['classMetadata'] = $classMetadata;
            $options[$alias]['identifierFieldName'] = $identifierFieldName;

            $this->aliases[$class] = $alias;
        }
        $this->options = $options;

        $this->multiple = $multiple;
        $this->separator = $separator;
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
            if (!is_array($value)) {
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

        $unorderedEntities = [];
        foreach ($this->options as $alias => $entityOptions) {
            $entityIds = MixedEntityUtils::unwrapValues($ids, $alias);

            /** @var EntityLoaderInterface $loader */
            $loader = $entityOptions['loader'];
            $identifierFieldName = $entityOptions['identifierFieldName'];

            $unorderedEntities = array_merge($unorderedEntities, $loader->getEntitiesByIds($identifierFieldName, $entityIds));
        }

        $entitiesById = [];
        $entities = [];
        foreach ($unorderedEntities as $entity) {
            $id = $this->getIdentifierValue($entity);
            $entitiesById[$id] = $entity;
        }
        foreach ($ids as $index => $id) {
            if (isset($entitiesById[$id])) {
                $entities[$index] = $entitiesById[$id];
            }
        }

        if (count($ids) !== count($entities)) {
            throw new TransformationFailedException('Could not find all matching entities for the given ids');
        }

        if (!$this->multiple) {
            return $entities[0];
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

        $class = ClassUtils::getRealClass(get_class($entity));
        if (!array_key_exists($class, $this->aliases)) {
            throw new TransformationFailedException(sprintf(
                'Expected instance of "%s", instance of "%s" given',
                implode(', ', array_keys($this->aliases)),
                $class
            ));
        }

        $alias = $this->aliases[$class];

        $id = (string) current($this->options[$alias]['classMetadata']->getIdentifierValues($entity));

        return MixedEntityUtils::wrapValue($id, $alias);
    }
}