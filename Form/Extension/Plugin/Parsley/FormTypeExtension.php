<?php

namespace ITE\FormBundle\Form\Extension\Plugin\Parsley;

use ITE\FormBundle\Service\Validation\ConstraintMapperInterface;
use ITE\FormBundle\SF\Plugin\ParsleyPlugin;
use ITE\FormBundle\Util\FormUtils;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

/**
 * Class FormTypeExtension
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
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
        if (!$form->isRoot() || !FormUtils::isFormHasPlugin($form, ParsleyPlugin::getName())) {
            return;
        }

        $constraints = $this->constraintMapper->map($view, $form);

        if (!isset($view->vars['plugins'])) {
            $view->vars['plugins'] = [];
        }
        $view->vars['plugins'][ParsleyPlugin::getName()] = [
            'extras' => [
                'constraints' => $constraints
            ],
            'options' => (object) array_replace_recursive($this->options, $options['plugins'][ParsleyPlugin::getName()]),
        ];
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
