<?php

namespace ITE\FormBundle\Form\Core\Extension;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class FormTypeExtension
 * @package ITE\FormBundle\Form\Core\Extension
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
     * @param SessionInterface $session
     * @param $dataTimezone
     */
    public function __construct(SessionInterface $session, $dataTimezone)
    {
        $this->viewTimezone = $session->get('timezone', $dataTimezone);
        $this->modelTimezone = $dataTimezone;
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
        }
        $ajax = isset($options['ajax']) ? $options['ajax'] : false;
        if ($form->isRoot()) {
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