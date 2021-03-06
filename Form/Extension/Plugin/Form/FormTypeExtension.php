<?php

namespace ITE\FormBundle\Form\Extension\Plugin\Form;

use ITE\FormBundle\SF\Plugin\FormPlugin;
use ITE\FormBundle\Util\FormUtils;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

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
     * @param $options
     */
    public function __construct($options)
    {
        $this->options = $options;
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        if (!$form->isRoot() || !FormUtils::isFormHasPlugin($form, FormPlugin::getName())) {
            return;
        }

        if (!isset($view->vars['plugins'])) {
            $view->vars['plugins'] = [];
        }
        $view->vars['plugins'][FormPlugin::getName()] = [
            'extras' => (object) [],
            'options' => (object) array_replace_recursive($this->options, $options['plugins'][FormPlugin::getName()]),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getExtendedType()
    {
        return 'form';
    }
}
