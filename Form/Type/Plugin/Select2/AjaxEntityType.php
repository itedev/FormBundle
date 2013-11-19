<?php

namespace ITE\FormBundle\Form\Type\Plugin\Select2;

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
     * @param $options
     * @param RouterInterface $router
     */
    public function __construct($options, RouterInterface $router)
    {
        $this->options = $options;
        $this->router = $router;
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
            'allow_create' => array('bool')
        ));
        $resolver->setOptional(array(
            'create_route',
        ));
        $resolver->setAllowedValues(array(
            'multiple' => array(false),
            'expanded' => array(false),
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        if ($form->getData()) {
            $choices = $view->vars['choices'];
            $value = $view->vars['value'];
            $defaultValue = array();

            if ($options['multiple']) {
                // multiple
                $defaultValue = array();
                foreach ($choices as $choice) {
                    /** @var $choice ChoiceView */
                    if ($value == $choice->value) {
                        $defaultValue[] = array(
                            'id' => $choice->value,
                            'text' => $choice->label
                        );
                    }
                }
            } else {
                // single
                foreach ($choices as $choice) {
                    /** @var $choice ChoiceView */
                    if ($value == $choice->value) {
                        $defaultValue = array(
                            'id' => $choice->value,
                            'text' => $choice->label
                        );
                        break;
                    }
                }
            }
            $view->vars['attr']['data-default-value'] = json_encode($defaultValue);
        }

        $view->vars['attr']['data-property'] = $options['property'];

        $view->vars['element_data'] = array(
            'extras' => array_replace_recursive(
                array(
                    'ajax' => true
                ),
                $options['extras'],
                array(
                    'allow_create' => $options['allow_create'],
                    'create_url' => $options['create_url'],
                )
            ),
            'options' => array_replace_recursive($this->options, $options['plugin_options'], array(
                'ajax' => array(
                    'url' => $options['url'],
                )
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