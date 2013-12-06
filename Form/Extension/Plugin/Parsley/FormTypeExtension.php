<?php

namespace ITE\FormBundle\Form\Extension\Plugin\Parsley;

use ITE\FormBundle\Service\Validation\ConstraintMapperInterface;
use ITE\FormBundle\SF\SFForm;
use ITE\FormBundle\Util\FormUtils;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class FormTypeExtension
 * @package ITE\FormBundle\Form\Extension\Plugin\Parsley
 */
class FormTypeExtension extends AbstractTypeExtension
{
    /**
     * @var array $options
     */
    protected $options;

    /**
     * @var ConstraintMapperInterface $constraintMapper
     */
    protected $constraintMapper;

    /**
     * @param $options
     * @param ConstraintMapperInterface $constraintMapper
     */
    public function __construct($options, ConstraintMapperInterface $constraintMapper)
    {
        $this->options = $options;
        $this->constraintMapper = $constraintMapper;
    }

    /**
     * {@inheritdoc}
     */
    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        if (!$form->isRoot() || !FormUtils::isFormHasPlugin($form, SFForm::PLUGIN_PARSLEY)) {
            return;
        }

        $constraints = $this->constraintMapper->map($view, $form);

        if (!isset($view->vars['plugins'])) {
            $view->vars['plugins'] = array();
        }
        $view->vars['plugins'][SFForm::PLUGIN_PARSLEY] = array(
            'extras' => array(
                'constraints' => $constraints
            ),
            'options' => (object) array_replace_recursive($this->options, $options['plugins'][SFForm::PLUGIN_PARSLEY]),
        );
        $view->vars['attr']['parsley-validate'] = '';
    }

    /**
     * {@inheritdoc}
     */
    public function getExtendedType()
    {
        return 'form';
    }
} 