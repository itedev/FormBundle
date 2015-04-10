<?php

namespace ITE\FormBundle\EntityConverter;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\Exception\StringCastException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessor;

/**
 * Class EntityConverter
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class DefaultConverter implements ConverterInterface
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
     * @param array $entities
     * @param null $labelPath
     * @return array
     */
    public function convert($entities, $labelPath = null)
    {
        if (!is_array($entities) && !($entities instanceof \Traversable)) {
            throw new \InvalidArgumentException('You must pass "array" or instance of "Traversable"');
        }

        if (empty($entities)) {
            return array();
        }

        $first = true;
        $valuePath = null;
        $choices = array();
        foreach ($entities as $entity) {
            if ($first) {
                $valuePath = $this->getValuePath($entity);
                $labelPath = $this->getLabelPathFromRequest($labelPath);
                $first = false;
            }

            $value = $this->getValue($entity, $valuePath);
            $label = $this->getLabel($entity, $labelPath);

            $choices[] = array(
                'value' => $value,
                'label' => $label,
            );
        }

        return $choices;
    }

    /**
     * @param null $labelPath
     * @return string|null
     */
    protected function getLabelPathFromRequest($labelPath = null)
    {
        if (!isset($labelPath)) {
            $request = $this->requestStack->getMasterRequest();

            $property = $request->query->get('property');
            $property = !empty($property) ? $property : null;

            return $property;
        }

        return $labelPath;
    }

    /**
     * @param object $entity
     * @param string|null $labelPath
     * @return string
     */
    protected function getLabel($entity, $labelPath)
    {
        $label = null;
        if ($labelPath) {
            $label = (string) $this->propertyAccessor->getValue($entity, $labelPath);
        } elseif (is_object($entity) && method_exists($entity, '__toString')) {
            $label = (string) $entity;
        } else {
            throw new StringCastException(sprintf('A "__toString()" method was not found on the objects of type "%s" passed to the choice field. To read a custom getter instead, set the argument $labelPath to the desired property path.', get_class($entity)));
        }

        return $label;
    }

    /**
     * @param object $entity
     * @return string
     */
    protected function getValuePath($entity)
    {
        $metadata = $this->em->getClassMetadata(get_class($entity));

        return $metadata->getSingleIdentifierFieldName();
    }

    /**
     * @param object $entity
     * @param string $valuePath
     * @return mixed
     */
    protected function getValue($entity, $valuePath)
    {
        return $this->propertyAccessor->getValue($entity, $valuePath);
    }
}