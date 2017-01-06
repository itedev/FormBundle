<?php

namespace ITE\FormBundle\Form\ChoiceList;

use \Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bridge\Doctrine\Form\ChoiceList\EntityLoaderInterface;
use Symfony\Component\Form\Extension\Core\ChoiceList\ObjectChoiceList;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

/**
 * Class AjaxEntityChoiceList
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class AjaxEntityChoiceList extends ObjectChoiceList
{
    /**
     * @var ObjectManager
     */
    private $em;

    /**
     * @var string
     */
    private $class;

    /**
     * @var ClassMetadata
     */
    private $classMetadata;

    /**
     * @var EntityLoaderInterface
     */
    private $loader;

    /**
     * @var string
     */
    private $identifierFieldName;

    /**
     * @param ObjectManager $em
     * @param null|string $class
     * @param null $labelPath
     * @param EntityLoaderInterface|null $loader
     * @param PropertyAccessorInterface|null $propertyAccessor
     */
    public function __construct(
        ObjectManager $em,
        $class,
        $labelPath = null,
        EntityLoaderInterface $loader = null,
        PropertyAccessorInterface $propertyAccessor = null
    ) {
        $this->em = $em;
        $this->classMetadata = $em->getClassMetadata($class);
        $this->class = $this->classMetadata->getName();
        $this->loader = $loader;
        $this->identifierFieldName = $this->classMetadata->getSingleIdentifierFieldName();

        parent::__construct([], $labelPath, [], null, null, $propertyAccessor);
    }

    /**
     * @param mixed $data
     */
    public function setData($data)
    {
        if (!is_array($data) && !($data instanceof \Traversable)) {
            $data = [$data];
        }
        parent::initialize($data, [], []);
    }

    /**
     * {@inheritdoc}
     */
    public function getChoicesForValues(array $values)
    {
        $unorderedEntities = $this->loader->getEntitiesByIds($this->identifierFieldName, $values);
        $entitiesById = [];
        $entities = [];

        foreach ($unorderedEntities as $entity) {
            $value = $this->getIdentifierValue($entity);
            $entitiesById[$value] = $entity;
        }

        foreach ($values as $i => $value) {
            if (isset($entitiesById[$value])) {
                $entities[$i] = $entitiesById[$value];
            }
        }

        return $entities;
    }

    /**
     * {@inheritdoc}
     */
    public function getValuesForChoices(array $entities)
    {
        $values = [];

        foreach ($entities as $i => $entity) {
            if ($entity instanceof $this->class) {
                $values[$i] = $this->getIdentifierValue($entity);
            }
        }

        return $values;
    }

    /**
     * {@inheritdoc}
     */
    protected function createValue($entity)
    {
        return $this->getIdentifierValue($entity);
    }

    /**
     * {@inheritdoc}
     */
    protected function createIndex($entity)
    {
        return $this->getIdentifierValue($entity);
    }

    /**
     * @param object $entity
     * @return string
     */
    private function getIdentifierValue($entity)
    {
        return (string) current($this->classMetadata->getIdentifierValues($entity));
    }
}
