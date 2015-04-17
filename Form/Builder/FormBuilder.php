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
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

/**
 * Class FormBuilder
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
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
     * @param string|array $parents
     * @param string|FormTypeInterface $type
     * @param array $options
     * @param null $formModifier
     * @return $this|FormBuilderInterface
     */
    public function addHierarchical($child, $parents, $type = null, array $options = array(), $formModifier = null)
    {
        if (!is_string($parents) && !is_array($parents)) {
            throw new UnexpectedTypeException($parents, 'string or array');
        }
        if (empty($parents)) {
            throw new \InvalidArgumentException('You must set at least one parent');
        }
        if (!is_array($parents)) {
            $parents = array($parents);
        }
        foreach ($parents as $parent) {
            if (!$this->has($parent)) {
                throw new \InvalidArgumentException(sprintf('FormBuilder does not contain "%s" child'));
            }
        }
        if (!is_callable($formModifier)) {
            throw new \InvalidArgumentException('The form modifier handler must be a valid PHP callable.');
        }

        $options = array_merge($options, [
            'hierarchical_parents' => $parents,
        ]);

        $propertyAccessor = $this->propertyAccessor;

        // PRE_SET_DATA event listener for root builder
        $this
            ->addEventListener(FormEvents::PRE_SET_DATA, function(FormEvent $event) use ($child, $type, $options, $parents, $formModifier, $propertyAccessor) {
                $form = $event->getForm();
                $data = $event->getData();

                $parentValues = [];
                foreach ($parents as $parent) {
                    $parentValues[$parent] = isset($data)
                        ? $propertyAccessor->getValue($data, $parent)
                        : null;
                }

                $params = $parentValues;
                array_unshift($params, $options);
//                array_unshift($params, $form);

                $modifiedOptions = call_user_func_array($formModifier, $params);

                $form->add($child, $type, $modifiedOptions);
            })
        ;

        // POST SUBMIT event listeners for parent builders
        $parentValues = [];
        $this->addEventListener(HierarchicalFormEvents::PARENT_POST_SUBMIT, function(HierarchicalFormEvent $event) use ($child, $type, $options, $parents, $formModifier, &$parentValues) {
            if ($child !== $event->getChildName() || !in_array($event->getParentName(), $parents)) {
                return;
            }
            $parentValues[$event->getParentName()] = $event->getParentData();

            if (count($parents) === count($parentValues)) {
                // keep parent name order
                $parentValues = array_merge(array_flip($parents), $parentValues);
                $form = $event->getForm();

                $params = $parentValues;
                array_unshift($params, $options);
//                array_unshift($params, $form);
                $parentValues = [];

                $modifiedOptions = call_user_func_array($formModifier, $params);

                $form->add($child, $type, $modifiedOptions);
            }
        });

        $that = $this;
        foreach ($parents as $parent) {
            $this
                ->get($parent)
                ->addEventListener(FormEvents::POST_SUBMIT, function(FormEvent $event) use ($child, $parent, $that) {
                    $hierarchicalEvent = new HierarchicalFormEvent(
                        $event->getForm()->getParent(),
                        $child,
                        $parent,
                        $event->getForm()->getData()
                    );
                    $that->getEventDispatcher()->dispatch(HierarchicalFormEvents::PARENT_POST_SUBMIT, $hierarchicalEvent);
                })
            ;
        }

        return parent::add($child, $type, $options);
    }

    /**
     * @param int|string|FormBuilderInterface $child
     * @param string|FormTypeInterface $type
     * @param array $options
     * @param null $formModifier
     * @return $this|FormBuilderInterface
     */
    public function addDynamic($child, $type = null, array $options = array(), $formModifier = null)
    {
        $children = $this->all();
        if (empty($children)) {
            throw new \RuntimeException('You cannot add dynamic field to empty FormBuilder');
        }
        end($children);
        $sibling = key($children);

        $this
            ->addEventListener(FormEvents::PRE_SET_DATA, function(FormEvent $event) use ($child, $type, $options, $formModifier) {
                $form = $event->getForm();
                $data = $event->getData();

                $params = [$options, $data];

                if (is_callable($formModifier)) {
                    $options = call_user_func_array($formModifier, $params);
                }

                $form->add($child, $type, $options);
            })
        ;
        $this
            ->get($sibling)
            ->addEventListener(FormEvents::POST_SUBMIT, function(FormEvent $event) use ($child, $type, $options, $formModifier) {
                $form = $event->getForm()->getParent();
                $data = $form->getData();

                $params = [$options, $data];

                if (is_callable($formModifier)) {
                    $options = call_user_func_array($formModifier, $params);
                }

                $form->add($child, $type, $options);
            })
        ;

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