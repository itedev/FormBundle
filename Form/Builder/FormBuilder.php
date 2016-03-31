<?php

namespace ITE\FormBundle\Form\Builder;

use ITE\Common\Util\ReflectionUtils;
use ITE\FormBundle\Form\Builder\Event\HierarchicalEvent;
use ITE\FormBundle\Form\Builder\Event\Model\HierarchicalParent;
use ITE\FormBundle\Form\EventListener\Component\Hierarchical\HierarchicalSetDataSubscriber;
use ITE\FormBundle\Form\EventListener\Component\Hierarchical\HierarchicalAddChildSubscriber;
use ITE\FormBundle\Form\Form;
use ITE\FormBundle\Form\FormInterface;
use ITE\FormBundle\FormAccess\FormAccess;
use ITE\FormBundle\FormAccess\FormAccessorInterface;
use ITE\FormBundle\Proxy\ProxyFactory;
use ITE\FormBundle\Util\EventDispatcherUtils;
use ITE\FormBundle\Util\FormUtils;
use ProxyManager\Configuration;
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
     * @var ProxyFactory $proxyFactory
     */
    protected $proxyFactory;

    /**
     * @var PropertyAccessorInterface $propertyAccessor
     */
    protected $propertyAccessor;

    /**
     * @var FormAccessorInterface
     */
    protected $formAccessor;

    /**
     * @var array $formHashes
     */
    protected $formHashes = [];

    /**
     * {@inheritdoc}
     */
    public function __construct(
        ProxyFactory $proxyFactory,
        $name,
        $dataClass,
        EventDispatcherInterface $dispatcher,
        FormFactoryInterface $factory,
        array $options = []
    ) {
        parent::__construct($name, $dataClass, $dispatcher, $factory, $options);

        $this->proxyFactory = $proxyFactory;
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

        $formAccessor = $this->formAccessor;
        $formHashes = $this->formHashes;
        $proxy = $this->proxyFactory->createProxy(
            $form,
            [
                'setData' => function (FormInterface $proxy, FormInterface $instance, $method, $params, $returnEarly) use ($formAccessor, &$formHashes) {
                    $form = $instance->getParent();
                    $child = $instance->getName();
                    $type = $instance->getConfig()->getType()->getInnerType();
                    $options = $instance->getConfig()->getOptions();
                    $parents = $instance->getConfig()->getOption('hierarchical_parents');
                    $formModifier = $instance->getConfig()->getOption('hierarchical_modifier');

                    if (empty($parents)) {
                        return;
                    }

                    $formHash = spl_object_hash($form->get($child));
                    if (in_array($formHash, $formHashes)) {
                        return;
                    }

                    if ($form->isSubmitted()) {
                        return;
                    }

                    $hierarchicalParents = [];
                    $parentDatas = [];
                    foreach ($parents as $parentName) {
                        $parentForm = $formAccessor->getForm($form, $parentName);
                        $parentData = $parentForm ? $parentForm->getData() : null;

                        $hierarchicalParent = new HierarchicalParent($parentName, $parentData, $parentForm);
                        $hierarchicalParents[$parentName] = $hierarchicalParent;
                        $parentDatas[$parentName] = $parentData;
                    }

                    $hierarchicalEvent = new HierarchicalEvent($form, $hierarchicalParents, $options);

                    $params = $parentDatas;
                    array_unshift($params, $hierarchicalEvent);

                    if (false === call_user_func_array($formModifier, $params)) {
                        // save old form hash
                        $formHashes[] = $formHash;

                        return;
                    }

                    //$oldEd = $form->get($this->child)->getConfig()->getEventDispatcher();
                    $form->add($child, $type, $hierarchicalEvent->getOptions());
                    //$newEd = $form->get($this->child)->getConfig()->getEventDispatcher();
                    //EventDispatcherUtils::extend($newEd, $oldEd);

                    $instanceFieldName = $proxy->__sleep();
                    ReflectionUtils::setValue($proxy, $instanceFieldName[0], $form->get($child));

                    // save new form hash
                    $formHashes[] = spl_object_hash($form->get($child));
                },
                'submit' => function (FormInterface $proxy, FormInterface $instance, $method, $params, $returnEarly) use ($formAccessor, &$formHashes) {
                    $form = $instance->getParent();
                    $child = $instance->getName();
                    $type = $instance->getConfig()->getType()->getInnerType();
                    $options = $instance->getConfig()->getOptions();
                    $parents = $instance->getConfig()->getOption('hierarchical_parents');
                    $formModifier = $instance->getConfig()->getOption('hierarchical_modifier');

                    if (empty($parents)) {
                        return;
                    }

                    //if (!$form->has($child)) {
                    //    return;
                    //}

                    $formHash = spl_object_hash($form->get($child));

                    $rootForm = $form->getRoot();
                    $originator = $rootForm->getConfig()->getAttribute('hierarchical_originator');

                    $hierarchicalParents = [];
                    $parentDatas = [];
                    foreach ($parents as $parentName) {
                        $parentForm = $this->formAccessor->getForm($form, $parentName);
                        $parentData = $parentForm ? $parentForm->getData() : null;

                        $parentFullName = FormUtils::getFullName($parentForm);
                        $isParentOriginator = null !== $originator
                            ? in_array($parentFullName, $originator)
                            : false;

                        $hierarchicalParent = new HierarchicalParent($parentName, $parentData, $parentForm, $isParentOriginator);
                        $hierarchicalParents[$parentName] = $hierarchicalParent;
                        $parentDatas[$parentName] = $parentData;
                    }

                    $hierarchicalEvent = new HierarchicalEvent($form, $hierarchicalParents, $options, true, $originator);

                    $params = $parentDatas;
                    array_unshift($params, $hierarchicalEvent);

                    if (false === call_user_func_array($formModifier, $params)) {
                        // save old form hash
                        $formHashes[] = $formHash;

                        $form->get($child)->setRawOption('hierarchical_changed', false);

                        return;
                    }

                    //$oldEd = $form->get($this->child)->getConfig()->getEventDispatcher();
                    $form->add($child, $type, $hierarchicalEvent->getOptions());
                    //$newEd = $form->get($this->child)->getConfig()->getEventDispatcher();
                    //EventDispatcherUtils::extend($newEd, $oldEd);

                    $instanceFieldName = $proxy->__sleep();
                    ReflectionUtils::setValue($proxy, $instanceFieldName[0], $form->get($child));

                    // save new form hash
                    $formHashes[] = spl_object_hash($form->get($child));
                },
            ],
            [
                'setData' => function (FormInterface $proxy, FormInterface $instance, $method, $params, $returnEarly) use ($formAccessor) {
                    if (!$instance->getConfig()->hasOption('hierarchical_data')) {
                        return;
                    }

                    $data = $instance->getConfig()->getOption('hierarchical_data');
                    FormUtils::setData($instance, $data);
                },
                'submit' => function (FormInterface $proxy, FormInterface $instance, $method, $params, $returnEarly) use ($formAccessor) {
                    if (!$instance->getConfig()->hasOption('hierarchical_data')) {
                        return;
                    }

                    $data = $instance->getConfig()->getOption('hierarchical_data');
                    FormUtils::setData($instance, $data);
                },
            ]
        );

        return $proxy;
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
        //if (!is_callable($formModifier)) {
        //    throw new \InvalidArgumentException('The form modifier handler must be a valid PHP callable.');
        //}

        $options = array_merge(
            $options,
            [
                'hierarchical_parents' => $parents,
                'hierarchical_modifier' => $formModifier,
            ]
        );

        $this->add($child, $type, $options);

        //$this->get($child)->addEventSubscriber(new HierarchicalSetDataSubscriber());
        //
        //// evaluate reference point
        //$referenceLevelUp = false;
        //$reference = FormUtils::getBuilderReference($this, $child, $referenceLevelUp);
        //$reference->addEventSubscriber(
        //    new HierarchicalAddChildSubscriber(
        //        $child,
        //        $type,
        //        $options,
        //        $parents,
        //        $formModifier,
        //        $referenceLevelUp,
        //        $this->formAccessor
        //    )
        //);

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
     * {@inheritdoc}
     */
    public function replaceType($name, $type, $modifier = null)
    {
        $child = $this->get($name);
        $options = $child->getOptions();

        if (is_callable($modifier)) {
            $options = call_user_func($modifier, $options);
        }

        return $this->add($name, $type, $options);
    }

    /**
     * {@inheritdoc}
     */
    public function replaceOptions($name, $modifier)
    {
        $child = $this->get($name);
        $options = $child->getOptions();
        $type = $child->getType()->getName();

        if (is_callable($modifier)) {
            $options = call_user_func($modifier, $options);
        }

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
