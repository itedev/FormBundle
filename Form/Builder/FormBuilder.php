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
use ProxyManager\Proxy\ValueHolderInterface;
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
     * @var string $formClass
     */
    protected $formClass;

    /**
     * @var FormAccessorInterface
     */
    protected $formAccessor;

    /**
     * @param ProxyFactory $proxyFactory
     * @param string $formClass
     * @param EventDispatcherInterface $name
     * @param FormFactoryInterface $dataClass
     * @param EventDispatcherInterface $dispatcher
     * @param FormFactoryInterface $factory
     * @param array $options
     */
    public function __construct(
        ProxyFactory $proxyFactory,
        $formClass,
        $name,
        $dataClass,
        EventDispatcherInterface $dispatcher,
        FormFactoryInterface $factory,
        array $options = []
    ) {
        parent::__construct($name, $dataClass, $dispatcher, $factory, $options);

        $this->proxyFactory = $proxyFactory;
        $this->formClass = $formClass;
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
    public function getOriginalType()
    {
        return $this->getOption('original_type');
    }

    /**
     * {@inheritdoc}
     */
    public function getOriginalOptions()
    {
        return $this->getOption('original_options');
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
        /** @var Form $form */
        $form = new $this->formClass($this->getFormConfig());

        foreach ($children as $child) {
            // Automatic initialization is only supported on root forms
            $form->add($child->setAutoInitialize(false)->getForm());
        }

        if ($this->getAutoInitialize()) {
            // Automatically initialize the form if it is configured so
            $form->initialize();
        }

        if (null === $form->getConfig()->getOption('hierarchical_parents')) {
            return $form;
        }

        $formAccessor = $this->formAccessor;
        $proxy = $this->proxyFactory->createProxy(
            $form,
            [
                'setData' => function (FormInterface $proxy, FormInterface $instance, $method, $params, $returnEarly) use ($formAccessor) {
                    // before setData() callback
                    /** @var self $config */
                    $config = $instance->getConfig();

                    $parents = $config->getOption('hierarchical_parents', []);
                    if (empty($parents)) {
                        return;
                    }
                    if ($config->getOption('skip_interceptors', false)) {
                        return;
                    }

                    $form = $instance->getParent();
                    $child = $instance->getName();
                    $type = $config->getOriginalType();
                    $options = $config->getOriginalOptions();
                    $callback = $config->getOption('hierarchical_callback');

                    $modelData = $params['modelData'];
//                    $oldOriginalData = $config->getOption('original_data');
                    $instance->setRawOption('original_data', $modelData);

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

                    $hierarchicalEvent = new HierarchicalEvent(
                        $form,
                        $type,
                        $hierarchicalParents,
                        $options,
                        $modelData
                    );

                    $params = $parentDatas;
                    array_unshift($params, $hierarchicalEvent);
                    if (false === call_user_func_array($callback, $params)) {
                        // if hierarchical callback return false - don't re-add child field
                        return;
                    }

                    //$oldEd = $form->get($this->child)->getConfig()->getEventDispatcher();
                    $form->add($child, $hierarchicalEvent->getType(), array_merge($hierarchicalEvent->getOptions(), [
                        'skip_interceptors' => true,
                        'original_data' => $modelData,
                    ]));
                    $newProxy = $form->get($child);
                    self::updateProxyValue($proxy, $newProxy);
                },
                'submit' => function (FormInterface $proxy, FormInterface $instance, $method, $params, $returnEarly) use ($formAccessor) {
                    // before submit() callback
                    /** @var self $config */
                    $config = $instance->getConfig();

                    $parents = $config->getOption('hierarchical_parents', []);
                    if (empty($parents)) {
                        return;
                    }
                    if ($config->getOption('skip_interceptors', false)) {
                        return;
                    }

                    $form = $instance->getParent();
                    $child = $instance->getName();
                    $type = $config->getOriginalType();
                    $options = $config->getOriginalOptions();
                    $callback = $config->getOption('hierarchical_callback');

                    //if (!$form->has($child)) {
                    //    return;
                    //}

                    $rootForm = $form->getRoot();
                    $originator = $rootForm->getConfig()->getAttribute('hierarchical_originator');

                    $hierarchicalParents = [];
                    $parentDatas = [];
                    $hierarchicalAffected = false;
                    foreach ($parents as $parentName) {
                        /** @var \ITE\FormBundle\Form\FormInterface $parentForm */
                        $parentForm = $this->formAccessor->getForm($form, $parentName);
                        $parentData = $parentForm ? $parentForm->getData() : null;

                        $parentFullName = FormUtils::getFullName($parentForm);
                        $isParentOriginator = null !== $originator
                            ? in_array($parentFullName, $originator)
                            : false;
                        if ($isParentOriginator || $parentForm->isHierarchicalAffected()) {
                            $hierarchicalAffected = true;
                        }

                        $hierarchicalParent = new HierarchicalParent(
                            $parentName,
                            $parentData,
                            $parentForm,
                            $isParentOriginator
                        );

                        $hierarchicalParents[$parentName] = $hierarchicalParent;
                        $parentDatas[$parentName] = $parentData;
                    }

                    $submittedData = $params['submittedData'];
                    //                    $modelData = FormUtils::getModelDataFromSubmittedData($instance, $submittedData);

                    $hierarchicalEvent = new HierarchicalEvent(
                        $form,
                        $type,
                        $hierarchicalParents,
                        $options,
                        $submittedData,
                        true,
                        $originator
                    );

                    $params = $parentDatas;
                    array_unshift($params, $hierarchicalEvent);
                    if (false === call_user_func_array($callback, $params)) {
                        // if hierarchical callback return false - don't re-add child field
                        $form->get($child)->setRawOption('hierarchical_changed', false);

                        return;
                    }

                    //$oldEd = $form->get($this->child)->getConfig()->getEventDispatcher();
                    $form->add($child, $hierarchicalEvent->getType(), array_merge($hierarchicalEvent->getOptions(), [
                        'skip_interceptors' => true,
                    ]));
                    $newProxy = $form->get($child);
                    self::updateProxyValue($proxy, $newProxy);

                    $proxy->setParameter('hierarchical_affected', $hierarchicalAffected);
                },
            ],
            [
                'setData' => function (FormInterface $proxy, FormInterface $instance, $method, $params, $returnEarly) use ($formAccessor) {
                    // after setData() callback
                    /** @var self $config */
                    $config = $instance->getConfig();

                    $parents = $config->getOption('hierarchical_parents', []);
                    if (empty($parents)) {
                        return;
                    }
                    if ($config->getOption('skip_interceptors', false)) {
                        $instance->unsetRawOption('skip_interceptors');

                        return;
                    }
                    if (!$config->hasOption('hierarchical_data')) {
                        return;
                    }

                    $data = $config->getOption('hierarchical_data');
                    $instance->unsetRawOption('hierarchical_data');
                    FormUtils::setData($instance, $data);
                },
                'submit' => function (FormInterface $proxy, FormInterface $instance, $method, $params, $returnEarly) use ($formAccessor) {
                    // after submit() callback
                    /** @var self $config */
                    $config = $instance->getConfig();

                    $parents = $config->getOption('hierarchical_parents', []);
                    if (empty($parents)) {
                        return;
                    }
                    if ($config->getOption('skip_interceptors', false)) {
                        $instance->unsetRawOption('skip_interceptors');

                        return;
                    }
                    if (!$config->hasOption('hierarchical_data')) {
                        return;
                    }

                    $data = $config->getOption('hierarchical_data');
                    $instance->unsetRawOption('hierarchical_data');
                    FormUtils::setData($instance, $data);
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
                if (false !== $options) {
                    $form->add($child, $type, $options);
                }
            }
        );

        return $this;
    }



    /**
     * {@inheritdoc}
     */
    public function add($child, $type = null, array $options = [])
    {
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
        /**
         * no need to unset:
         * - original_data (cannot be set here)
         * - hierarchical_data (cannot be set here)
         * - original_type (anyway will be overwritten below)
         * - original_options (anyway will be overwritten below)
         */

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
        $options = $child->getOriginalOptions();

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
        $type = $child->getOriginalType();
        $options = $child->getOriginalOptions();

        if (is_callable($callback)) {
            $options = call_user_func($callback, $options);
        }

        return $this->add($name, $type, $options);
    }

    /**
     * @param FormInterface $proxy
     * @param FormInterface $newProxy
     */
    private static function updateProxyValue(FormInterface $proxy, FormInterface $newProxy)
    {
        /** @var ValueHolderInterface $newProxy */
        $newInstance = $newProxy->getWrappedValueHolderValue();

        //$newEd = $child->getConfig()->getEventDispatcher();
        //EventDispatcherUtils::extend($newEd, $oldEd);

        $instanceFieldName = $proxy->__sleep();
        ReflectionUtils::setValue($proxy, $instanceFieldName[0], $newInstance);
    }
}
