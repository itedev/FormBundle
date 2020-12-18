<?php

namespace ITE\FormBundle\Form\EventListener;

use ITE\FormBundle\Form\Model\FormMappingItem;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

/**
 * Class ResizeSerializedCollectionSubscriber
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class ResizeSerializedCollectionSubscriber implements EventSubscriberInterface
{
    /**
     * @var PropertyAccessorInterface
     */
    private $propertyAccessor;

    /**
     * @var FormMappingItem
     */
    private $mapping;

    /**
     * ResizeSerializedCollectionSubscriber constructor.
     *
     * @param PropertyAccessorInterface $propertyAccessor
     * @param FormMappingItem $mapping
     */
    public function __construct(
        PropertyAccessorInterface $propertyAccessor,
        FormMappingItem $mapping
    ) {
        $this->propertyAccessor = $propertyAccessor;
        $this->mapping = $mapping;
    }

    /**
     * @param FormMappingItem $mappingItem
     * @param mixed $previousData
     * @param mixed $data
     * @return mixed
     */
    private function mapFormsToDataRecursive(FormMappingItem $mappingItem, &$previousData, $data)
    {
        if (is_object($previousData) && !$mappingItem->getByReference()) {
            $previousData = clone $previousData;
        }

        foreach ($mappingItem->getChildren() as $name => $childMappingItem) {
            $propertyPath = $childMappingItem->getPropertyPath();
            if (null !== $propertyPath && $childMappingItem->getMapped()) {
                $previousChildData = $this->propertyAccessor->getValue($previousData, $name);
                $childData = $this->propertyAccessor->getValue($data, $name);

                if ($childMappingItem->hasChildren()) {
                    $normChildData = $previousChildData;

                    if ($childMappingItem->isCollection()) {
                        // collection
                        if (is_object($previousChildData) && !$childMappingItem->getByReference()) {
                            $normChildData = clone $previousChildData;
                        }

                        $prototypeMappingItem = $childMappingItem->getPrototype();

                        foreach ($normChildData as $name2 => $previousChildItem) {
                            if (isset($childData[$name2])) {
                                // existing item
                                $childItem = $childData[$name2];

                                $this->mapFormsToDataRecursive($prototypeMappingItem, $previousChildItem, $childItem);
                            } else {
                                // removed item
                                unset($normChildData[$name2]);
                            }
                        }
                        foreach ($childData as $name2 => $childItem) {
                            if (!isset($normChildData[$name2])) {
                                // added item
                                $normChildData[$name2] = $childItem;
                            }
                        }
                    } else {
                        // regular sub-form
                        $this->mapFormsToDataRecursive($childMappingItem, $normChildData, $childData);
                    }

                    $childData = $normChildData;
                }

                if ($childData instanceof \DateTime && $childData == $previousChildData) {
                    continue;
                }

                if (!is_object($previousData) || !$childMappingItem->getByReference() || $childData !== $previousChildData) {
                    $this->propertyAccessor->setValue($previousData, $name, $childData);
                }
            }
        }

        return $previousData;
    }

    /**
     * {@inheritdoc}
     */
    public function onSubmit(FormEvent $event)
    {
        $form = $event->getForm();
        $data = $event->getData();

        $previousData = $form->getData();

        foreach ($previousData as $name => $previousItem) {
            if (isset($data[$name])) {
                // existing item
                $item = $data[$name];

                $this->mapFormsToDataRecursive($this->mapping, $previousItem, $item);
            } else {
                // removed item
                unset($previousData[$name]);
            }
        }
        
        if (is_array($data) || ($data instanceof \Traversable)) {
            foreach ($data as $name => $item) {
                if (!isset($previousData[$name])) {
                    // added item
                    $previousData[$name] = $item;
                }
            }
        }

        $event->setData($previousData);
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            FormEvents::SUBMIT => ['onSubmit', 50],
        ];
    }
}
