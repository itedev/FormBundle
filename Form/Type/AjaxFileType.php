<?php

namespace ITE\FormBundle\Form\Type;

use ITE\FormBundle\Form\DataTransformer\FileToAjaxDataTransformer;
use ITE\FormBundle\SF\Form\ClientFormTypeInterface;
use ITE\FormBundle\SF\Form\ClientFormView;
use ITE\FormBundle\Util\FormUtils;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\Routing\RouterInterface;

/**
 * Class AjaxFileType
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class AjaxFileType extends AbstractType implements ClientFormTypeInterface
{
    /**
     * @var RouterInterface
     */
    protected $router;

    /**
     * @var RequestStack
     */
    protected $requestStack;

    /**
     * @var PropertyAccessorInterface
     */
    protected $propertyAccessor;

    /**
     * @param RouterInterface $router
     * @param RequestStack $requestStack
     */
    public function __construct(RouterInterface $router, RequestStack $requestStack)
    {
        $this->router = $router;
        $this->requestStack = $requestStack;
        $this->propertyAccessor = PropertyAccess::createPropertyAccessor();
    }

    /**
     * @return RouterInterface
     */
    public function getRouter()
    {
        return $this->router;
    }

    /**
     * @return null|Request
     */
    public function getRequest()
    {
        return $this->requestStack->getMasterRequest();
    }

    /**
     * @return PropertyAccessorInterface
     */
    public function getPropertyAccessor()
    {
        return $this->propertyAccessor;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add($options['file_name'], $options['file_type'], array_merge($options['file_options'], [
                'required' => $options['required'],
                'multiple' => $options['multiple'],
            ]))
            ->add($options['data_name'], $options['data_type'], array_merge($options['data_options'], [
                'multiple' => $options['multiple'],
            ]))
            ->addViewTransformer(new FileToAjaxDataTransformer(
                $options['file_name'],
                $options['data_name'],
                $options['multiple']
            ))
//            ->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) use ($fileName, $dataName, $self) {
//                $form = $event->getForm();
//                $data = $event->getData();
//                $request = $self->getRequest();
//                $propertyPath = FormUtils::getPropertyPath($form);
//
//                $newData = array_key_exists($dataName, $data) ? $data[$dataName] : $data;
//
//                $files = $request->files->all();
//                $propertyAccessor = $self->getPropertyAccessor();
//                $propertyAccessor->setValue($files, $propertyPath, $newData);
//                $request->files->replace($files);
//            })
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function buildClientView(ClientFormView $clientView, FormView $view, FormInterface $form, array $options)
    {
        $clientView->setOption('child_names', [
            'file' => $options['file_name'],
            'data' => $options['data_name'],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $self = $this;
        $urlNormalizer = function (Options $options, $url) use ($self) {
            if (!empty($url)) {
                return $url;
            } elseif (!empty($options['route'])) {
                return $self->getRouter()->generate($options['route'], $options['route_parameters']);
            } else {
                return null;
            }
        };

        $resolver->setDefaults([
            'url' => null,
            'route' => null,
            'route_parameters' => [],
//            'data_class' => 'Symfony\Component\HttpFoundation\File\File',
//            'empty_data' => [],
            'multiple' => false,
            'file_name' => 'file',
            'file_type' => 'file',
            'file_options' => [],
            'data_name' => 'data',
            'data_type' => 'ite_ajax_file_data',
            'data_options' => [],
            'error_bubbling' => false,
        ]);
        $resolver->setNormalizers([
            'url' => $urlNormalizer,
        ]);
        $resolver->setAllowedTypes([
            'route' => ['null', 'string'],
            'route_parameters' => ['array'],
            'url' => ['null', 'string'],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'ite_ajax_file';
    }
}
