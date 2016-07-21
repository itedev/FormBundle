<?php

namespace ITE\FormBundle\Form\Builder;

use ITE\Common\Util\ReflectionUtils;
use ITE\FormBundle\Form\Builder\Event\HierarchicalEvent;
use ITE\FormBundle\Form\Builder\Event\Model\HierarchicalParent;
use ITE\FormBundle\Form\Form;
use ITE\FormBundle\Form\FormInterface;
use ITE\FormBundle\Form\DataMapper\PropertyPathMapper;
use ITE\FormBundle\FormAccess\FormAccess;
use ITE\FormBundle\FormAccess\FormAccessorInterface;
use ITE\FormBundle\Proxy\ProxyFactory;
use ITE\FormBundle\Util\FormUtils;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\Exception\BadMethodCallException;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Form\FormBuilder as BaseFormBuilder;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormFactoryInterface;

/**
 * Class FormBuilder
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class FormBuilder extends BaseFormBuilder implements FormBuilderInterface
{
    /**
     * @var ProxyFactory $proxyFactory
     */
    protected $proxyFactory;

    /**
     * @var FormAccessorInterface
     */
    protected $formAccessor;

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
        // set overridden data mapper
        $this->setDataMapper($this->getCompound() ? new PropertyPathMapper() : null);
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
        $proxy = $this->proxyFactory->createProxy(
            $form,
            [
                'setData' => function (FormInterface $proxy, FormInterface $instance, $method, $params, $returnEarly) use ($formAccessor) {
                    $form = $instance->getParent();
                    $child = $instance->getName();
                    $type = $instance->getConfig()->getOption('original_type');
                    $options = $instance->getConfig()->getOption('original_options');
                    $parents = $instance->getConfig()->getOption('hierarchical_parents', []);
                    $callback = $instance->getConfig()->getOption('hierarchical_callback');

                    $modelData = $params['modelData'];
                    $oldOriginalData = $instance->getConfig()->getOption('original_data');
                    $instance->setRawOption('original_data', $modelData);

                    if (empty($parents)) {
                        return;
                    }

                    if ($instance->getConfig()->getOption('skip_interceptors', false)) {
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

                    $hierarchicalEvent = new HierarchicalEvent($form, $hierarchicalParents, $options, $modelData);

                    $params = $parentDatas;
                    array_unshift($params, $hierarchicalEvent);

                    if (false === call_user_func_array($callback, $params)) {
                        return;
                    }

                    //$oldEd = $form->get($this->child)->getConfig()->getEventDispatcher();
                    $form->add($child, $type, array_merge($hierarchicalEvent->getOptions(), [
                        'skip_interceptors' => true,
                        'original_data' => $modelData,
                    ]));

                    $newInstance = $form->get($child)->getWrappedValueHolderValue();

                    //$newEd = $form->get($this->child)->getConfig()->getEventDispatcher();
                    //EventDispatcherUtils::extend($newEd, $oldEd);

                    $instanceFieldName = $proxy->__sleep();
                    ReflectionUtils::setValue($proxy, $instanceFieldName[0], $newInstance);
                },
                'submit' => function (FormInterface $proxy, FormInterface $instance, $method, $params, $returnEarly) use ($formAccessor) {
                    $form = $instance->getParent();
                    $child = $instance->getName();
                    $type = $instance->getConfig()->getOption('original_type');
                    $options = $instance->getConfig()->getOption('original_options');
                    $parents = $instance->getConfig()->getOption('hierarchical_parents', []);
                    $callback = $instance->getConfig()->getOption('hierarchical_callback');

                    if (empty($parents)) {
                        return;
                    }

                    if ($instance->getConfig()->getOption('skip_interceptors', false)) {
                        return;
                    }

                    //if (!$form->has($child)) {
                    //    return;
                    //}

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

                    $submittedData = $params['submittedData'];
                    //                    $modelData = FormUtils::getModelDataFromSubmittedData($instance, $submittedData);

                    $hierarchicalEvent = new HierarchicalEvent($form, $hierarchicalParents, $options, $submittedData, true, $originator);

                    $params = $parentDatas;
                    array_unshift($params, $hierarchicalEvent);

                    if (false === call_user_func_array($callback, $params)) {
                        $form->get($child)->setRawOption('hierarchical_changed', false);

                        return;
                    }

                    //$oldEd = $form->get($this->child)->getConfig()->getEventDispatcher();
                    $form->add($child, $type, array_merge($hierarchicalEvent->getOptions(), [
                        'skip_interceptors' => true,
                    ]));
                    //$newEd = $form->get($this->child)->getConfig()->getEventDispatcher();
                    //EventDispatcherUtils::extend($newEd, $oldEd);

                    $instanceFieldName = $proxy->__sleep();
                    ReflectionUtils::setValue($proxy, $instanceFieldName[0], $form->get($child)->getWrappedValueHolderValue());
                },
            ],
            [
                'setData' => function (FormInterface $proxy, FormInterface $instance, $method, $params, $returnEarly) use ($formAccessor) {
                    $parents = $instance->getConfig()->getOption('hierarchical_parents', []);

                    if (empty($parents)) {
                        return;
                    }
                    if ($instance->getConfig()->getOption('skip_interceptors', false)) {
                        $instance->unsetRawOption('skip_interceptors');
                        return;
                    }
                    if (!$instance->getConfig()->hasOption('hierarchical_data')) {
                        return;
                    }

                    $data = $instance->getConfig()->getOption('hierarchical_data');
                    FormUtils::setData($instance, $data);
                    $instance->unsetRawOption('hierarchical_data');
                },
                'submit' => function (FormInterface $proxy, FormInterface $instance, $method, $params, $returnEarly) use ($formAccessor) {
                    $parents = $instance->getConfig()->getOption('hierarchical_parents', []);

                    if (empty($parents)) {
                        return;
                    }
                    if ($instance->getConfig()->getOption('skip_interceptors', false)) {
                        $instance->unsetRawOption('skip_interceptors');
                        return;
                    }
                    if (!$instance->getConfig()->hasOption('hierarchical_data')) {
                        return;
                    }

                    $data = $instance->getConfig()->getOption('hierarchical_data');
                    FormUtils::setData($instance, $data);
                    $instance->unsetRawOption('hierarchical_data');
                },
            ]
        );

        return $proxy;
    }

    /**
     * {@inheritdoc}
     */
    public function addHierarchical($child, $parents, $type = null, array $options = [], $callback = null)
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
            $parents = [$parents];
        }
        //if (!is_callable($callback)) {
        //    throw new \InvalidArgumentException('The hierarchical callback must be a valid PHP callable.');
        //}

        $options = array_merge($options, [
            'hierarchical_parents' => $parents,
            'hierarchical_callback' => $callback,
        ]);
        $this->add($child, $type, $options);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function addDataAware($child, $type = null, $callback = null)
    {
        if (!is_callable($callback)) {
            throw new \InvalidArgumentException('The form modifier handler must be a valid PHP callable.');
        }
        $this->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) use ($child, $type, $callback) {
                $form = $event->getForm();
                $data = $event->getData();

                $options = call_user_func($callback, $data);
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
        //        $originalOptions = $options;
        //        if (isset($originalOptions['skip_interceptors'])) {
        //            unset($originalOptions['skip_interceptors']);
        //        }
        //        if (isset($originalOptions['original_type'])) {
        //            unset($originalOptions['original_type']);
        //        }
        //        if (isset($originalOptions['original_options'])) {
        //            unset($originalOptions['original_options']);
        //        }
        //        if (isset($originalOptions['original_data'])) {
        //            unset($originalOptions['original_data']);
        //        }
        //
        ////        if ($this->getOption('skip_interceptors', false)) {
        ////            $options['skip_interceptors'] = true;
        ////        }
        //        $options = array_merge($options, [
        //            'original_type' => $type,
        //            'original_options' => $originalOptions,
        //        ]);

        return parent::add($child, $type, $options);
    }

    /**
     * {@inheritdoc}
     */
    public function create($name, $type = null, array $options = [])
    {
        $originalOptions = $options;
        if (isset($originalOptions['skip_interceptors'])) {
            unset($originalOptions['skip_interceptors']);
        }
        // commented code below is redundant (ideally)
        //if (isset($originalOptions['original_data'])) {
        //    unset($originalOptions['original_data']);
        //}
        //if (isset($originalOptions['original_type'])) {
        //    unset($originalOptions['original_type']);
        //}
        //if (isset($originalOptions['original_options'])) {
        //    unset($originalOptions['original_options']);
        //}

        //if ($this->getOption('skip_interceptors', false)) {
        //    $options['skip_interceptors'] = true;
        //}

        $options = array_merge($options, [
            'original_type' => $type,
            'original_options' => $originalOptions,
        ]);

        return parent::create($name, $type, $options);
    }

    /**
     * {@inheritdoc}
     */
    public function remove($name)
    {
        return parent::remove($name);
    }

    /**
     * {@inheritdoc}
     */
    public function replaceType($name, $type, $callback = null)
    {
        $child = $this->get($name);
        $options = $child->getOption('original_options');

        if (is_callable($callback)) {
            $options = call_user_func($callback, $options);
        }

        return $this->add($name, $type, $options);
    }

    /**
     * {@inheritdoc}
     */
    public function replaceOptions($name, $callback)
    {
        $child = $this->get($name);
        $type = $child->getOption('original_type');
        $options = $child->getOption('original_options');

        if (is_callable($callback)) {
            $options = call_user_func($callback, $options);
        }

        return $this->add($name, $type, $options);
    }
}
