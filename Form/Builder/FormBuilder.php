<?php

namespace ITE\FormBundle\Form\Builder;

use ITE\Common\Util\ReflectionUtils;
use ITE\FormBundle\Form\Builder\Event\HierarchicalEvent;
use ITE\FormBundle\Form\Builder\Event\Model\HierarchicalParent;
use ITE\FormBundle\Form\Form;
use ITE\FormBundle\FormAccess\FormAccess;
use ITE\FormBundle\FormAccess\FormAccessorInterface;
use ITE\FormBundle\Util\FormUtils;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\Exception\BadMethodCallException;
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
     * @var array $formHashes
     */
    protected $formHashes = [];

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
            throw new BadMethodCallException('FormBuilder methods cannot be accessed anymore once the builder is turned into a FormConfigInterface instance.');
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

        $formAccessor = $this->formAccessor;
        $formHashes =& $this->formHashes;

        parent::add($child, $type, $options);

//        $childrenIndices = array_keys(ReflectionUtils::getValue($this, 'children'));
//        $index = array_search($child, $childrenIndices);
//        $reference = (0 !== $index)
//            ? $this->get($childrenIndices[$index - 1])
//            : $this;

        // FormEvents::PRE_SET_DATA
//        $reference
//          ->addEventListener(FormEvents::POST_SET_DATA, function(FormEvent $event)
        $this
            ->get($child)
            ->addEventListener(FormEvents::PRE_SET_DATA, function(FormEvent $event)
            use ($child, $type, $options, $parents, $formModifier, $formAccessor, &$formHashes) {
                $form = $event->getForm()->getParent();
                $data = $event->getData();

                $formHash = spl_object_hash($event->getForm());
                if (in_array($formHash, $formHashes) || $form->isSubmitted()) {
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

                $hierarchicalEvent = new HierarchicalEvent($form, $data, $hierarchicalParents, $options);

                $params = $parentDatas;
                array_unshift($params, $hierarchicalEvent);

                if (false === call_user_func_array($formModifier, $params)) {
                    // save old form hash
                    $formHashes[] = $formHash;

                    return;
                }

                $ed = $form->get($child)->getConfig()->getEventDispatcher();
                $form->add($child, $type, $hierarchicalEvent->getOptions());
                FormUtils::setEventDispatcher($form->get($child), $ed);

                // save new form hash
                $formHashes[] = spl_object_hash($form->get($child));
            })
        ;

        // FormEvents::PRE_SUBMIT
//        $reference
//          ->addEventListener(FormEvents::POST_SUBMIT, function(FormEvent $event)
        $this
            ->get($child)
            ->addEventListener(FormEvents::PRE_SUBMIT, function(FormEvent $event)
            use ($child, $type, $options, $parents, $formModifier, $formAccessor, &$formHashes) {
                $form = $event->getForm()->getParent();
                $data = $event->getData();

                $formHash = spl_object_hash($event->getForm());

                $rootForm = $form->getRoot();
                $originator = $rootForm->getConfig()->getAttribute('hierarchical_originator');

                $hierarchicalParents = [];
                $parentDatas = [];
                foreach ($parents as $parentName) {
                    $parentForm = $formAccessor->getForm($form, $parentName);
                    $parentData = $parentForm ? $parentForm->getData() : null;

                    $parentFullName = FormUtils::getFullName($parentForm);
                    $isParentOriginator = null !== $originator
                        ? in_array($parentFullName, $originator)
                        : false;

                    $hierarchicalParent = new HierarchicalParent($parentName, $parentData, $parentForm, $isParentOriginator);
                    $hierarchicalParents[$parentName] = $hierarchicalParent;
                    $parentDatas[$parentName] = $parentData;
                }

                $hierarchicalEvent = new HierarchicalEvent($form, $data, $hierarchicalParents, $options, true, $originator);

                $params = $parentDatas;
                array_unshift($params, $hierarchicalEvent);

                if (false === call_user_func_array($formModifier, $params)) {
                    // save old form hash
                    $formHashes[] = $formHash;

                    return;
                }

                $ed = $form->get($child)->getConfig()->getEventDispatcher();
                $form->add($child, $type, $hierarchicalEvent->getOptions());
                FormUtils::setEventDispatcher($form->get($child), $ed);

                // save new form hash
                $formHashes[] = spl_object_hash($form->get($child));
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

}