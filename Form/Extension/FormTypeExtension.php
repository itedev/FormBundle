<?php

namespace ITE\FormBundle\Form\Extension;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class FormTypeExtension
 * @package ITE\FormBundle\Form\Extension
 */
class FormTypeExtension extends AbstractTypeExtension
{
    /**
     * @var string
     */
    protected $viewTimezone;

    /**
     * @var string
     */
    protected $modelTimezone;

    /**
     * @var Request $request
     */
    protected $request;

    /**
     * @param SessionInterface $session
     * @param Request $request
     * @param $modelTimezone
     */
    public function __construct(SessionInterface $session, Request $request, $modelTimezone)
    {
        $this->viewTimezone = $session->get('timezone', $modelTimezone);
        $this->request = $request;
        $this->modelTimezone = $modelTimezone;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'model_timezone' => $this->modelTimezone,
            'view_timezone' => $this->viewTimezone,
            'position' => null,
        ));
        $resolver->setOptional(array(
            'ajax',
        ));
        $resolver->setAllowedTypes(array(
            'ajax' => 'bool',
        ));

        $resolver->setDefaults(array(
            'plugin_options' => array(),
            'extras' => array(),
            'error_bubbling' => false,
        ));
        $resolver->setAllowedTypes(array(
            'plugin_options' => array('array'),
            'extras' => array('array'),
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        if ($form->isRoot()) {
            $view->vars['submitted'] = $form->isSubmitted();

            // ajax
            $ajax = isset($options['ajax']) ? $options['ajax'] : false;
            $view->vars['ajax'] = $ajax;
            if (!isset($view->vars['attr']['id'])) {
                $view->vars['attr']['id'] = $view->vars['id'];
            }

            $view->vars['element_data'] = array(
                'extras' => (object) $options['extras'],
                'options' => (object) $options['plugin_options']
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getExtendedType()
    {
        return 'form';
    }
}