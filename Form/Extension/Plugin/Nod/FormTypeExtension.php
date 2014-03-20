<?php

namespace ITE\FormBundle\Form\Extension\Plugin\Nod;

use ITE\FormBundle\Service\Validation\ConstraintMapperInterface;
use ITE\FormBundle\SF\Plugin\NodPlugin;
use ITE\FormBundle\Util\FormUtils;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

/**
 * Class FormTypeExtension
 * @package ITE\FormBundle\Form\Extension\Plugin\Nod
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
        if (!$form->isRoot() || !FormUtils::isFormHasPlugin($form, NodPlugin::NAME)) {
            return;
        }

        $constraints = $this->constraintMapper->map($view, $form);

        if (!isset($view->vars['plugins'])) {
            $view->vars['plugins'] = array();
        }
        $view->vars['plugins'][NodPlugin::NAME] = array(
            'extras' => (object) array(),
            'options' => array(
                'metrics' => $constraints,
                'options' => (object) array_replace_recursive($this->options, $options['plugins'][NodPlugin::NAME]),
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getExtendedType()
    {
        return 'form';
    }
} 