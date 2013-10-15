<?php

namespace ITE\FormBundle\Service;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\Exception\StringCastException;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessor;

/**
 * Class EntityConverter
 * @package ITE\FormBundle\Service
 */
class EntityConverter implements EntityConverterInterface
{
    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @var PropertyAccessor
     */
    protected $propertyAccessor;

    /**
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
        $this->propertyAccessor = PropertyAccess::createPropertyAccessor();
    }

    /**
     * @param $entity
     * @param null $labelPath
     * @param null $idPath
     * @return array
     * @throws StringCastException
     */
    public function convertEntityToOption($entity, $labelPath = null, $idPath = null)
    {
        if (!isset($idPath)) {
            $idPath = $this->getEntityIdentifier($entity);
        }

        if ($labelPath) {
            $label = $this->propertyAccessor->getValue($entity, $labelPath);
        } elseif (is_object($entity) && method_exists($entity, '__toString')) {
            $label = (string) $entity;
        } else {
            throw new StringCastException(sprintf('A "__toString()" method was not found on the objects of type "%s" passed to the choice field. To read a custom getter instead, set the argument $labelPath to the desired property path.', get_class($entity)));
        }

        return array(
            'id' => $this->propertyAccessor->getValue($entity, $idPath),
            'label' => $label
        );
    }

    /**
     * @param $entities
     * @param null $labelPath
     * @return array
     */
    public function convertEntitiesToOptions($entities, $labelPath = null)
    {
        $options = array();

        if (empty($entities)) {
            return $options;
        }

        $entity = reset($entities);
        $idPath = $this->getEntityIdentifier($entity);

        foreach ($entities as $entity) {
            $options[] = $this->convertEntityToOption($entity, $labelPath, $idPath);
        }

        return $options;
    }

    /**
     * @param $entity
     * @return mixed
     */
    protected function getEntityIdentifier($entity)
    {
        $meta = $this->em->getClassMetadata(get_class($entity));

        $idFieldNames = $meta->getIdentifierFieldNames();
        $idPath = array_shift($idFieldNames);

        return $idPath;
    }
}