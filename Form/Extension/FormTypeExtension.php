<?php

namespace ITE\FormBundle\Form\Extension;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
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
     * @param SessionInterface $session
     * @param $modelTimezone
     */
    public function __construct(SessionInterface $session, $modelTimezone)
    {
        $this->viewTimezone = $session->get('timezone', $modelTimezone);
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
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['submitted'] = $form->isSubmitted();
    }

    /**
     * {@inheritdoc}
     */
    public function getExtendedType()
    {
        return 'form';
    }
}