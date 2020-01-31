<?php

namespace ITE\FormBundle\Component\Editable;

use ITE\FormatterBundle\Formatter\FormatterManagerInterface;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Symfony\Component\Routing\RouterInterface;

/**
 * Class EditableManager
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class EditableManager implements EditableManagerInterface
{
    /**
     * @var RegistryInterface
     */
    protected $registry;

    /**
     * @var RouterInterface
     */
    protected $router;

    /**
     * @var FormFactoryInterface $formFactory
     */
    protected $formFactory;

    /**
     * @var EngineInterface
     */
    protected $templating;

    /**
     * @var FormatterManagerInterface
     */
    protected $formatterManager;

    /**
     * @var array
     */
    protected $defaults = [];

    /**
     * @var PropertyAccessor
     */
    protected $propertyAccessor;

    /**
     * @param RegistryInterface $registry
     * @param RouterInterface $router
     * @param FormFactoryInterface $formFactory
     * @param EngineInterface $templating
     * @param FormatterManagerInterface $formatterManager
     * @param array $defaults
     */
    public function __construct(
        RegistryInterface $registry,
        RouterInterface $router,
        FormFactoryInterface $formFactory,
        EngineInterface $templating,
        FormatterManagerInterface $formatterManager,
        array $defaults = []
    ) {
        $this->registry = $registry;
        $this->router = $router;
        $this->formFactory = $formFactory;
        $this->templating = $templating;
        $this->formatterManager = $formatterManager;
        $this->defaults = $defaults;
        $this->propertyAccessor = PropertyAccess::createPropertyAccessor();
    }

    /**
     * {@inheritdoc}
     */
    public function getWidget($entity, $field, array $options = [])
    {
        $resolvedOptions = $this->resolveOptions($options);

        $class = get_class($entity);
        $manager = $this->registry->getManagerForClass($class);
        $classMetadata = $manager->getClassMetadata($class);
        $identifier = $classMetadata->getIdentifierValues($entity);

        $form = null;
        if ($resolvedOptions['inline']) {
            $form = $this->getForm($entity, $field, $resolvedOptions);
        }

        return $this->render($entity, $field, $options, $resolvedOptions, $class, $identifier, $form);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function handleRequest(Request $request)
    {
        $requestData = $this->getRequestData($request);
        $class = $requestData['class'];
        $identifier = $requestData['identifier'];
        $field = $requestData['field'];
        $options = $requestData['options'];

        $manager = $this->registry->getManagerForClass($class);
        $entity = $manager->getRepository($class)->find($identifier);

        $resolvedOptions = $this->resolveOptions($options);

        $form = $this->getForm($entity, $field, $resolvedOptions);
        $form->handleRequest($request);
        if ($form->isValid()) {
            $manager->flush();
        }

        return new JsonResponse([
            'success' => $form->isValid(),
            'html' => $this->render($entity, $field, $options, $resolvedOptions, $class, $identifier, $form),
        ]);
    }

    /**
     * @param object $entity
     * @param string $field
     * @param array $options
     * @return FormInterface
     */
    public function createForm($entity, $field, array $options = [])
    {
        $resolvedOptions = $this->resolveOptions($options);

        return $this->getForm($entity, $field, $resolvedOptions);
    }

    /**
     * {@inheritdoc}
     */
    public function getRequestData(Request $request)
    {
        $class = $request->request->get('class');
        $identifier = json_decode($request->request->get('identifier'), true);
        $field = $request->request->get('field');
        $options = json_decode($request->request->get('options'), true);

        return [
            'class' => $class,
            'identifier' => $identifier,
            'field' => $field,
            'options' => $options,
        ];
    }

    /**
     * @param object entity
     * @param string $field
     * @param array $options
     * @param array $resolvedOptions
     * @param string $class
     * @param array $identifier
     * @param FormInterface|null $form
     * @return string
     */
    protected function render(
        $entity,
        $field,
        array $options,
        array $resolvedOptions,
        $class,
        $identifier,
        FormInterface $form = null
    ) {
        $value = $this->propertyAccessor->getValue($entity, $field);
        if (null !== $resolvedOptions['text_template']) {
            $text = $this->templating->render($resolvedOptions['text_template'], [
                'entity' => $entity,
                'field' => $field,
                'value' => $value,
            ]);
        } else {
            if (null !== $resolvedOptions['formatter']) {
                $text = $this->formatterManager->format($value, $resolvedOptions['formatter'], $resolvedOptions['formatter_options']);
            } else {
                $text = $this->formatterManager->formatProperty($entity, $field, $resolvedOptions['formatter_options']);
            }
        }

        $parameters = [
            'text' => $text,
            'class' => $class,
            'identifier' => $identifier,
            'field' => $field,
            'options' => array_merge($options, [
                'form_name' => $resolvedOptions['form_name'],
            ]),
            'inline' => $resolvedOptions['inline'],
            'format' => $resolvedOptions['format'],
            'container_attr' => $resolvedOptions['container_attr'],
            'edit_link_href' => $resolvedOptions['edit_link_href'],
            'edit_link_attr' => $resolvedOptions['edit_link_attr'],
        ];
        if ($resolvedOptions['inline']) {
            $parameters['form'] = $form->createView();
        }

        return $this->templating->render($resolvedOptions['template'], $parameters);
    }

    /**
     * {@inheritdoc}
     */
    protected function configureOptions(OptionsResolver $resolver)
    {
        $router = $this->router;

        $urlNormalizer = function (Options $options, $url) use ($router) {
            if (!empty($url)) {
                return $url;
            } elseif (!empty($options['route'])) {
                return $router->generate($options['route'], $options['route_parameters']);
            } else {
                return null;
            }
        };

        $resolver->setDefaults([
            'inline' => true,
            'format' => 'html',
            'text_template' => null,
            'formatter' => null,
            'formatter_options' => $this->defaults['formatter_options'],
            'url' => null,
            'route' => $this->defaults['route'],
            'route_parameters' => $this->defaults['route_parameters'],
            'form_name' => function (Options $options) {
                return uniqid('ite_editable_');
            },
            'form_options' => $this->defaults['form_options'],
            'field_type' => null,
            'field_options' => $this->defaults['field_options'],
            'field_options_modifier' => null,
            'template' => $this->defaults['template'],
            'container_attr' => [],
            'edit_link_href' => '#',
            'edit_link_attr' => [],
        ]);
        $resolver->setNormalizers([
            'url' => $urlNormalizer,
        ]);
        $resolver->setAllowedTypes([
            'inline' => ['bool'],
            'format' => ['string'],
            'text_template' => ['null', 'string'],
            'formatter' => ['null', 'string'],
            'formatter_options' => ['array'],
            'form_name' => ['string'],
            'form_options' => ['array'],
            'field_type' => ['null', 'string'],
            'field_options' => ['array'],
            'field_options_modifier' => ['null', 'callable'],
            'template' => ['string'],
            'container_attr' => ['array'],
            'edit_link_href' => ['string'],
            'edit_link_attr' => ['array'],
        ]);
    }

    /**
     * @param array $options
     * @return array
     */
    protected function resolveOptions(array $options)
    {
        $resolver = new OptionsResolver();
        $this->configureOptions($resolver);
        $options = $resolver->resolve($options);

        return $options;
    }

    /**
     * @param $entity
     * @param $field
     * @param array $options
     * @return Form
     */
    protected function getForm($entity, $field, $options = [])
    {
        $fieldOptions = array_merge([
            'label' => false,
        ], $options['field_options']);
        if (is_callable($options['field_options_modifier'])) {
            $fieldOptions = call_user_func_array($options['field_options_modifier'], [
                $entity,
                $fieldOptions
            ]);
        }

        return $this->formFactory
            ->createNamedBuilder($options['form_name'], 'form', $entity, array_merge([
                'data_class' => get_class($entity),
                'csrf_protection' => false,
            ], $options['form_options'], [
                'action' => $options['url'],
            ]))
            ->add($field, $options['field_type'], $fieldOptions)
            ->getForm()
        ;
    }
}
