<?php

namespace ITE\FormBundle\Service\Converter;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\Exception\StringCastException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
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
     * @var Request $requestStack
     */
    protected $requestStack;

    /**
     * @var PropertyAccessor
     */
    protected $propertyAccessor;

    /**
     * @param EntityManager $em
     * @param RequestStack $requestStack
     */
    public function __construct(EntityManager $em, RequestStack $requestStack)
    {
        $this->em = $em;
        $this->requestStack = $requestStack;
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
        if (!is_array($entities) && !($entities instanceof \Traversable)) {
            throw new \InvalidArgumentException('You must pass "array" or instance of "Traversable"');
        }

        if (empty($entities)) {
            return array();
        }

        $options = array();
        $first = true;
        $idPath = null;

        foreach ($entities as $entity) {
            if ($first) {
                $idPath = $this->getEntityIdentifier($entity);
                $labelPath = $this->getLabelPathFromRequest($labelPath);
                $first = false;
            }
            $options[] = $this->internalConvertEntityToOption($entity, $labelPath, $idPath);
        }

        return $options;
    }

    /**
     * @param $entity
     * @param null $labelPath
     * @return array
     */
    public function convertEntityToChoice($entity, $labelPath = null)
    {
        $option = $this->convertEntityToOption($entity, $labelPath);

        return array(
            $option['value'] => $option['label']
        );
    }

    /**
     * @param $entities
     * @param null $labelPath
     * @return array
     */
    public function convertEntitiesToChoices($entities, $labelPath = null)
    {
        $options = $this->convertEntitiesToOptions($entities, $labelPath);

        $choices = array();
        foreach ($options as $option) {
            $choices[$option['value']] = $option['label'];
        }

        return $choices;
    }

    /**
     * @param null $labelPath
     * @return string|null
     */
    protected function getLabelPathFromRequest($labelPath = null)
    {
        $request = $this->requestStack->getCurrentRequest();
        if (!isset($labelPath) && $request->query->has('property')) {
            return $request->query->get('property');
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
     * @return string
     */
    protected function getEntityIdentifier($entity)
    {
        $metadata = $this->em->getClassMetadata(get_class($entity));

        return $metadata->getSingleIdentifierFieldName();
    }
}