<?php

namespace ITE\FormBundle\Service\Converter;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\Exception\StringCastException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessor;

/**
 * Class EntityConverter
 * @package ITE\FormBundle\Service\Converter
 */
class EntityConverter implements EntityConverterInterface
{
    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @var Request $request
     */
    protected $request;

    /**
     * @var PropertyAccessor
     */
    protected $propertyAccessor;

    /**
     * @param EntityManager $em
     * @param Request $request
     */
    public function __construct(EntityManager $em, Request $request)
    {
        $this->em = $em;
        $this->request = $request;
        $this->propertyAccessor = PropertyAccess::createPropertyAccessor();
    }

    /**
     * @param $entity
     * @param null $labelPath
     * @return array
     */
    public function convertEntityToOption($entity, $labelPath = null)
    {
        $idPath = $this->getEntityIdentifier($entity);
        $labelPath = $this->getLabelPathFromRequest($labelPath);

        return $this->internalConvertEntityToOption($entity, $labelPath, $idPath);
    }

    /**
     * @param $entities
     * @param null $labelPath
     * @return array
     */
    public function convertEntitiesToOptions($entities, $labelPath = null)
    {
        if (empty($entities)) {
            return array();
        }

        $entity = reset($entities);
        $idPath = $this->getEntityIdentifier($entity);
        $labelPath = $this->getLabelPathFromRequest($labelPath);

        $options = array();
        foreach ($entities as $entity) {
            $options[] = $this->internalConvertEntityToOption($entity, $labelPath, $idPath);
        }

        return $options;
    }

    /**
     * @param null $labelPath
     * @return string|null
     */
    protected function getLabelPathFromRequest($labelPath = null)
    {
        if (!isset($labelPath) && $this->request->query->has('property')) {
            return $this->request->query->get('property');
        }

        return null;
    }

    /**
     * @param $entity
     * @param null|string $labelPath
     * @param null|string $idPath
     * @return array
     * @throws StringCastException
     */
    protected function internalConvertEntityToOption($entity, $labelPath, $idPath)
    {
        if ($labelPath) {
            $label = (string) $this->propertyAccessor->getValue($entity, $labelPath);
        } elseif (is_object($entity) && method_exists($entity, '__toString')) {
            $label = (string) $entity;
        } else {
            throw new StringCastException(sprintf('A "__toString()" method was not found on the objects of type "%s" passed to the choice field. To read a custom getter instead, set the argument $labelPath to the desired property path.', get_class($entity)));
        }

        return array(
            'value' => $this->propertyAccessor->getValue($entity, $idPath),
            'label' => $label
        );
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