<?php

namespace ITE\FormBundle\Form\Builder;

use ITE\FormBundle\Form\EventListener\Component\Hierarchical\HierarchicalSetDataSubscriber;
use ITE\FormBundle\Form\EventListener\Component\Hierarchical\HierarchicalAddChildSubscriber;
use ITE\FormBundle\Form\Form;
use ITE\FormBundle\FormAccess\FormAccess;
use ITE\FormBundle\FormAccess\FormAccessorInterface;
use ITE\FormBundle\Util\EventDispatcherUtils;
use ITE\FormBundle\Util\FormUtils;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\Exception\BadMethodCallException;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Form\FormBuilder as BaseFormBuilder;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

/**
 * Class FormBuilder
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class FormBuilder extends BaseFormBuilder implements FormBuilderInterface
{
    const HIERARCHICAL_ADD_CHILD_SUBSCRIBER_CLASS = 'ITE\FormBundle\Form\EventListener\Component\Hierarchical\HierarchicalAddChildSubscriber';
    const HIERARCHICAL_SET_DATA_SUBSCRIBER_CLASS = 'ITE\FormBundle\Form\EventListener\Component\Hierarchical\HierarchicalSetDataSubscriber';

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
    public function __construct(
        $name,
        $dataClass,
        EventDispatcherInterface $dispatcher,
        FormFactoryInterface $factory,
        array $options = []
    ) {
        parent::__construct($name, $dataClass, $dispatcher, $factory, $options);

        $this->propertyAccessor = PropertyAccess::createPropertyAccessor();
        $this->formAccessor = FormAccess::createFormAccessor();
    }

    /**
     * {@inheritdoc}
     */
    public function getFormAccessor()
    {
        return $this->formAccessor;
    }

    /**
     * {@inheritdoc}
     */
    public function getForm()
    {
        if ($this->locked) {
            throw new BadMethodCallException(
                'FormBuilder methods cannot be accessed anymore once the builder is turned into a FormConfigInterface instance.'
            );
        }

        $children = $this->all();

        $form = new Form($this->getFormConfig());

        foreach ($children as $child) {
            // Automatic initialization is only supported on root forms
            $form->add($child->setAutoInitialize(false)->getForm());
        }

        if ($this->getAutoInitialize()) {
            // Automatically initialize the form if it is configured so
            $form->initialize();
        }

        return $form;
    }

    /**
     * {@inheritdoc}
     */
    public function addHierarchical($child, $parents, $type = null, array $options = [], $formModifier = null)
    {
        if (!is_string($child)) {
            throw new UnexpectedTypeException($child, 'string');
        }
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

        $options = array_merge(
            $options,
            [
                'hierarchical_parents' => $parents,
            ]
        );

        $this->add($child, $type, $options);

        $this->get($child)->addEventSubscriber(new HierarchicalSetDataSubscriber());

        // evaluate reference point
        $referenceLevelUp = false;
        $reference = FormUtils::getBuilderReference($this, $child, $referenceLevelUp);
        $reference->addEventSubscriber(
            new HierarchicalAddChildSubscriber(
                $child,
                $type,
                $options,
                $parents,
                $formModifier,
                $referenceLevelUp,
                $this->formAccessor
            )
        );

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
        $this->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) use ($child, $type, $formModifier) {
                $form = $event->getForm();
                $data = $event->getData();

                $options = call_user_func($formModifier, $data);
                $form->add($child, $type, $options);
            }
        );

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function add($child, $type = null, array $options = [])
    {
        if ($this->has($child)) {
            $this->resetHierarchicalSubscribers($child);
        }

        return parent::add($child, $type, $options);
    }

    /**
     * {@inheritdoc}
     */
    public function remove($name)
    {
        $this->resetHierarchicalSubscribers($name);

        return parent::remove($name);
    }

    /**
     * @param $name
     * @param $type
     * @return $this|FormBuilderInterface
     */
    public function replaceType($name, $type)
    {
        $child = $this->get($name);
        $options = $child->getOptions();

        return $this->add($name, $type, $options);
    }

    /**
     * {@inheritdoc}
     */
    public function replaceOptions($name, array $options)
    {
        $child = $this->get($name);
        $currentOptions = $child->getOptions();
        $type = $child->getType()->getName();

        $options = array_replace_recursive($currentOptions, $options);

        return $this->add($name, $type, $options);
    }

    /**
     * @param string $name
     */
    protected function resetHierarchicalSubscribers($name)
    {
        $child = $this->get($name);

        $isHierarchical = $child->hasOption('hierarchical_parents');
        if ($isHierarchical) {
            $referenceLevelUp = false;
            $reference = FormUtils::getBuilderReference($this, $name, $referenceLevelUp);

            $rawEd = EventDispatcherUtils::getRawEventDispatcher($reference->getFormConfig()->getEventDispatcher());
            $listeners = EventDispatcherUtils::getRawListeners($rawEd);
            foreach ($listeners as $eventName => $priorityListeners) {
                foreach ($priorityListeners as $priority => $eventListeners) {
                    foreach ($eventListeners as $i => $eventListener) {
                        if (is_array($eventListener)
                            && is_object($eventListener[0])
                            && self::HIERARCHICAL_ADD_CHILD_SUBSCRIBER_CLASS === get_class($eventListener[0])) {
                            $rawEd->removeSubscriber($eventListener[0]);
                        }
                    }
                }
            }
        }
    }
}
