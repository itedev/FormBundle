<?php

namespace ITE\FormBundle\Form\Type\Plugin\Select2;

use ITE\FormBundle\SF\Plugin\Select2Plugin;
use ITE\FormBundle\Util\ArrayUtils;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class AjaxChoiceType
 * @package FormBundle\Form\Type\Plugin\Select2
 */
class AjaxChoiceType extends AbstractType
{
    /**
     * @var array $options
     */
    protected $options;

    /**
     * @param $options
     */
    public function __construct($options)
    {
        $this->options = $options;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'plugin_options' => array(),
        ));
        $resolver->setAllowedTypes(array(
            'plugin_options' => array('array'),
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
        $data = $form->getData();
        if ($options['empty_data'] !== $data && null !== $data) {
            $view->vars['attr']['data-default-value'] = json_encode($this->getDefaultValue($data, $options));
        }
        if (!isset($view->vars['plugins'])) {
            $view->vars['plugins'] = array();
        }
        $view->vars['plugins'][Select2Plugin::NAME] = array(
            'extras' => array(
                'ajax' => true,
            ),
            'options' => array_replace_recursive($this->options, $options['plugin_options'], array(
                'ajax' => array(
                    'url' => $options['url'],
                ),
                'multiple' => $options['multiple'],
                'allowClear' => false !== $options['empty_value'] && null !== $options['empty_value'],
            ))
        );
    }

    /**
     * @param $data
     * @param array $options
     * @return array
     */
    protected function getDefaultValue($data, array $options)
    {
        if (!$options['multiple']) {
            if (isset($options['choice_label_builder'])) {
                return array(
                    'id' => $data,
                    'text' => call_user_func($options['choice_label_builder'], $data)
                );
            } else {
                return array(
                    'id' => $data,
                    'text' => $options['choice_label'],
                );
            }
        } else {
            if (!is_array($data) || !$data instanceof \Traversable) {
                return [];
            }
            if (isset($options['choice_label_builder'])) {
                $choiceLabels = call_user_func($options['choice_label_builder'], $data);

                return ArrayUtils::arrayMapKey(function($label, $value) {
                    return array(
                        'id' => $value,
                        'text' => $label,
                    );
                }, $choiceLabels);
            } else {
                return array_map(function($item) use ($options) {
                    return array(
                        'id' => $item,
                        'text' => $options['choice_label'],
                    );
                }, $data);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'ite_ajax_choice';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'ite_select2_ajax_choice';
    }
} 