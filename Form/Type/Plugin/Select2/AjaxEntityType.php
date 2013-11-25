<?php

namespace ITE\FormBundle\Form\Type\Plugin\Select2;

use ITE\FormBundle\Form\DataTransformer\StringToArrayTransformer;
use ITE\FormBundle\Form\EventListener\ExplodeCollectionListener;
use ITE\FormBundle\Service\Converter\Plugin\Select2\EntityConverterInterface;
use RuntimeException;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\View\ChoiceView;
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
        $type = $this;
        $createUrl = function (Options $options) use ($type) {
            if ($options['allow_create']) {
                if (isset($options['create_route'])) {
                    return $type->getRouter()->generate($options['create_route']);
                }
                throw new RuntimeException('You must specify create_route when using true for allow_create option.');
            }
            return null;
        };
        $resolver->setDefaults(array(
            'plugin_options' => array(),
            'allow_create' => false,
            'create_url' => $createUrl,
            'error_bubbling' => false,
        ));
        $resolver->setAllowedTypes(array(
            'plugin_options' => array('array'),
            'allow_create' => array('bool'),
        ));
        $resolver->setOptional(array(
            'create_route',
        ));
        $resolver->setAllowedValues(array(
            'expanded' => array(false),
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if (!$options['multiple']) {
            return;
        }
        $builder->addEventSubscriber(new ExplodeCollectionListener());
        $builder->addViewTransformer(new StringToArrayTransformer());
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        if ($form->getData()) {
            $choices = $view->vars['choices'];

            $defaultValue = $options['multiple']
                ? $this->entityConverter->convertChoicesToOptions($choices)
                : $this->entityConverter->convertChoiceToOption(current($choices));

            $view->vars['attr']['data-default-value'] = json_encode($defaultValue);
        }

        $view->vars['attr']['data-property'] = $options['property'];

        $view->vars['element_data'] = array(
            'extras' => array(
                'ajax' => true,
                'allow_create' => $options['allow_create'],
                'create_url' => $options['create_url'],
            ),
            'options' => array_replace_recursive($this->options, $options['plugin_options'], array(
                'ajax' => array(
                    'url' => $options['url'],
                ),
                'multiple' => $options['multiple'],
            ))
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'ite_ajax_entity';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'ite_select2_ajax_entity';
    }
}