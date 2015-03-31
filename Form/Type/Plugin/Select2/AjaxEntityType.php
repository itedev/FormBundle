<?php

namespace ITE\FormBundle\Form\Type\Plugin\Select2;

use ITE\FormBundle\Form\ChoiceList\AjaxEntityChoiceList;
use ITE\FormBundle\Form\DataTransformer\StringToArrayTransformer;
use ITE\FormBundle\Form\EventListener\ExplodeCollectionListener;
use ITE\FormBundle\Service\Converter\Plugin\Select2\EntityConverterInterface;
use ITE\FormBundle\SF\Plugin\Select2Plugin;
use RuntimeException;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Routing\RouterInterface;

/**
 * Class AjaxEntityType
 * @package ITE\FormBundle\Form\Type\Plugin\Select2
 */
class AjaxEntityType extends AbstractType
{
    /**
     * @var array $options
     */
    protected $options;

    /**
     * @var RouterInterface $router
     */
    protected $router;

    /**
     * @var EntityConverterInterface $entityConverter
     */
    protected $entityConverter;

    /**
     * @param $options
     * @param RouterInterface $router
     * @param EntityConverterInterface $entityConverter
     */
    public function __construct($options, RouterInterface $router, EntityConverterInterface $entityConverter)
    {
        $this->options = $options;
        $this->router = $router;
        $this->entityConverter = $entityConverter;
    }

    /**
     * Get router
     *
     * @return RouterInterface
     */
    public function getRouter()
    {
        return $this->router;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $self = $this;

        $createUrl = function (Options $options) use ($self) {
            if ($options['allow_create']) {
                if (isset($options['create_route'])) {
                    return $self->getRouter()->generate($options['create_route']);
                }

                throw new RuntimeException('You must specify create_route when using true for allow_create option.');
            }

            return null;
        };

        $urlNormalizer = function (Options $options, $url) use ($self) {
            if (!empty($options['route'])) {
                return $self->getRouter()->generate($options['route'], $options['route_parameters']);
            } elseif (!empty($url)) {
                return $url;
            } else {
                throw new \RuntimeException('You must specify "route" or "url" option.');
            }
        };
        $resolver->setDefaults(array(
            'choices'  => array(),
            'choice_list' => function (Options $options) {
                return new AjaxEntityChoiceList(
                    $options['em'],
                    $options['class'],
                    $options['property']
                );
            },
            'allow_modify' => true,
            'plugin_options' => array(),
            'route' => null,
            'route_parameters' => array(),
            'url' => null,
            'allow_create' => false,
            'create_url' => $createUrl,
        ));
        $resolver->setNormalizers(array(
            'url' => $urlNormalizer,
        ));
        $resolver->setAllowedTypes(array(
            'plugin_options' => array('array'),
            'allow_create' => array('bool'),
        ));
        $resolver->setAllowedValues(array(
            'allow_modify' => array(true),
            'choices' => array(array()),
            'expanded' => array(false),
        ));
        $resolver->setOptional(array(
            'create_route',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['attr']['data-property'] = $options['property'];

        if (!isset($view->vars['plugins'])) {
            $view->vars['plugins'] = array();
        }
        $view->vars['plugins'][Select2Plugin::NAME] = array(
            'extras' => array(
                'ajax' => true,
                'allow_create' => $options['allow_create'],
                'create_url' => $options['create_url'],
            ),
            'options' => array_replace_recursive($this->options, $options['plugin_options'], array(
                'ajax' => array(
                    'url' => $options['url'],
                    'dataType' => 'json',
                ),
                'multiple' => $options['multiple'],
                'allowClear' => false !== $options['empty_value'] && null !== $options['empty_value'],
            ))
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'entity';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'ite_select2_ajax_entity';
    }
}