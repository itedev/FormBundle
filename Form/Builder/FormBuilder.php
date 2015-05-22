<?php

namespace ITE\FormBundle\Form\Builder;

use ITE\FormBundle\Form\Builder\Event\HierarchicalEvent;
use ITE\FormBundle\FormAccess\FormAccess;
use ITE\FormBundle\FormAccess\FormAccessorInterface;
use ITE\FormBundle\Util\FormUtils;
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
     * @var FormAccessorInterface
     */
    protected $formAccessor;

    /**
     * {@inheritdoc}
     */
    public function __construct($name, $dataClass, EventDispatcherInterface $dispatcher, FormFactoryInterface $factory,
        array $options = []
    )
    {
        parent::__construct($name, $dataClass, $dispatcher, $factory, $options);

        $this->propertyAccessor = PropertyAccess::createPropertyAccessor();
        $this->formAccessor = FormAccess::createFormAccessor();
    }

    /**
     * {@inheritdoc}
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
        if (!is_callable($formModifier)) {
            throw new \InvalidArgumentException('The form modifier handler must be a valid PHP callable.');
        }

        $options = array_merge($options, [
            'hierarchical_parents' => $parents,
        ]);

        $propertyAccessor = $this->propertyAccessor;
        $formAccessor = $this->formAccessor;

        parent::add($child, $type, $options);

        // FormEvents::PRE_SET_DATA
        $this
            ->get($child)
            ->addEventListener(FormEvents::PRE_SET_DATA, function(FormEvent $event)
            use ($child, $type, $options, $parents, $formModifier, $propertyAccessor, $formAccessor) {
                $form = $event->getForm()->getParent();
                if ($form->isSubmitted()) {
                    return;
                }

//                $data = $event->getData();
//                $parentValues = [];
//                foreach ($parents as $parent) {
//                    $parentValues[$parent] = isset($data)
//                        ? $propertyAccessor->getValue($data, $parent)
//                        : null;
//                }

                $parentValues = [];
                foreach ($parents as $parent) {
                    $parentForm = $formAccessor->getForm($form, $parent);
                    $parentValues[$parent] = $parentForm ? $parentForm->getData() : null;
                }

                $hierarchicalEvent = new HierarchicalEvent($form, $parentValues, $options);

                $params = $parentValues;
                array_unshift($params, $hierarchicalEvent);

                if (false === call_user_func_array($formModifier, $params)) {
                    return;
                }

                $ed = $form->get($child)->getConfig()->getEventDispatcher();
                $form->add($child, $type, $hierarchicalEvent->getOptions());
                FormUtils::setEventDispatcher($form->get($child), $ed);
            })
        ;

        // FormEvents::PRE_SUBMIT
        $this
            ->get($child)
            ->addEventListener(FormEvents::PRE_SUBMIT, function(FormEvent $event)
            use ($child, $type, $options, $parents, $formModifier, $formAccessor) {
                $form = $event->getForm()->getParent();

                $parentValues = [];
                foreach ($parents as $parent) {
                    $parentForm = $formAccessor->getForm($form, $parent);
                    $parentValues[$parent] = $parentForm ? $parentForm->getData() : null;
                }

                $rootForm = $form->getRoot();
                $originator = $rootForm->getConfig()->getAttribute('hierarchical_originator');
                $hierarchicalEvent = new HierarchicalEvent($form, $parentValues, $options, $originator, $formAccessor);

                $params = $parentValues;
                array_unshift($params, $hierarchicalEvent);

                if (false === call_user_func_array($formModifier, $params)) {
                    return;
                }

                $ed = $form->get($child)->getConfig()->getEventDispatcher();
                $form->add($child, $type, $hierarchicalEvent->getOptions());
                FormUtils::setEventDispatcher($form->get($child), $ed);
            })
        ;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function addDataAware($child, $type = null, $formModifier = null)
    {
        if (!is_callable($formModifier)) {
            throw new \InvalidArgumentException('The form modifier handler must be a valid PHP callable.');
        }
        $this->addEventListener(FormEvents::PRE_SET_DATA, function(FormEvent $event) use ($child, $type, $formModifier) {
            $form = $event->getForm();
            $data = $event->getData();

            $options = call_user_func($formModifier, $data);
            $form->add($child, $type, $options);
        });

        return $this;
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