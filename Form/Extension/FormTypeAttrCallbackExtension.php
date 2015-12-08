<?php

namespace ITE\FormBundle\Form\Extension;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class FormTypeAttrCallbackExtension
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class FormTypeAttrCallbackExtension extends AbstractTypeExtension
{
    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setOptional([
            'attr_callback',
        ]);
        $resolver->setAllowedTypes([
            'attr_callback' => ['callable'],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        if (!isset($options['attr_callback'])) {
            return;
        }

        $attr = $view->vars['attr'];
        $attr = call_user_func_array($options['attr_callback'], [$form, $attr]);
        if (is_array($attr)) {
            $view->vars = array_replace($view->vars, [
                'attr' => $attr,
            ]);
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
