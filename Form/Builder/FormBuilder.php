<?php

namespace ITE\FormBundle\Form\Builder;

use ITE\FormBundle\EventListener\Event\HierarchicalFormEvent;
use ITE\FormBundle\EventListener\HierarchicalFormEvents;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Form\FormBuilder as BaseFormBuilder;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

/**
 * Class FormBuilder
 * @package ITE\FormBundle\Form\Builder
 */
class FormBuilder extends BaseFormBuilder implements FormBuilderInterface
{
    /**
     * @var PropertyAccessorInterface $propertyAccessor
     */
    protected $propertyAccessor;

    /**
     * {@inheritdoc}
     */
    public function __construct($name, $dataClass, EventDispatcherInterface $dispatcher, FormFactoryInterface $factory, array $options = array())
    {
        parent::__construct($name, $dataClass, $dispatcher, $factory, $options);

        $this->propertyAccessor = PropertyAccess::createPropertyAccessor();
    }

    /**
     * @param int|string|FormBuilderInterface $child
     * @param string|array $parentNames
     * @param null $type
     * @param array $options
     * @param null $formModifier
     * @return $this|FormBuilderInterface
     */
    public function addHierarchical($child, $parentNames, $type = null, array $options = array(), $formModifier = null)
    {
        if (!is_string($parentNames) && !is_array($parentNames)) {
            throw new UnexpectedTypeException($parentNames, 'string or array');
        }
        if (empty($parentNames)) {
            throw new \InvalidArgumentException('You must set at least one parent');
        }
        if (!is_array($parentNames)) {
            $parentNames = array($parentNames);
        }
        foreach ($parentNames as $parentName) {
            if (!$this->has($parentName)) {
                throw new \InvalidArgumentException(sprintf('FormBuilder does not contain "%s" child'));
            }
        }

        $options = array_merge($options, [
            'hierarchical_parents' => $parentNames,
        ]);

        $propertyAccessor = $this->propertyAccessor;

        // PRE_SET_DATA event listener for root builder
        $preSetDataParentValueFetcher = function(FormEvent $event) use ($parentNames, $propertyAccessor) {
            $parentValues = [];
            $data = $event->getData();
            foreach ($parentNames as $parentName) {
                $parentValues[$parentName] = $propertyAccessor->getValue($data, $parentName);
            }

            return $parentValues;
        };

        $this
            ->addEventListener(FormEvents::PRE_SET_DATA, function(FormEvent $event) use ($formModifier, $preSetDataParentValueFetcher) {
                $params = array_merge(
                    [$event->getForm()],
                    $preSetDataParentValueFetcher($event)
                );
                call_user_func_array($formModifier, $params);
            })
        ;

        // POST SUBMIT event listeners for parent builders
        $childName = $child instanceof self
            ? $child->getName()
            : $child;

        $parentValues = [];
        $this->addEventListener(HierarchicalFormEvents::PARENT_POST_SUBMIT, function(HierarchicalFormEvent $event) use ($formModifier, $childName, $parentNames, &$parentValues) {
            if ($childName !== $event->getChildName() || !in_array($event->getParentName(), $parentNames)) {
                return;
            }
            $parentValues[$event->getParentName()] = $event->getParentData();

            if (count($parentNames) === count($parentValues)) {
                // keep parent name order
                $parentValues = array_merge(array_flip($parentNames), $parentValues);
                $params = array_merge(
                    [$event->getForm()],
                    $parentValues
                );
                $parentValues = [];
                call_user_func_array($formModifier, $params);
            }
        });

        $that = $this;
        foreach ($parentNames as $parentName) {
            $this
                ->get($parentName)
                ->addEventListener(FormEvents::POST_SUBMIT, function(FormEvent $event) use ($childName, $parentName, $that) {
                    $hierarchicalEvent = new HierarchicalFormEvent(
                        $event->getForm()->getParent(),
                        $childName,
                        $parentName,
                        $event->getForm()->getData()
                    );
                    $that->getEventDispatcher()->dispatch(HierarchicalFormEvents::PARENT_POST_SUBMIT, $hierarchicalEvent);
                })
            ;
        }

        return parent::add($child, $type, $options);
    }

    /**
     * @param $name
     * @param $type
     * @return $this|FormBuilderInterface
     */
    public function replaceType($name, $type)
    {
        $field = $this->get($name);
        $options = $field->getOptions();

        return $this->add($name, $type, $options);
    }

    /**
     * @param $name
     * @param $options
     * @return $this|FormBuilderInterface
     */
    public function replaceOptions($name, $options)
    {
        $field = $this->get($name);
        $currentOptions = $field->getOptions();
        $type = $field->getType()->getName();

        $options = array_replace_recursive($currentOptions, $options);

        return $this->add($name, $type, $options);
    }

}