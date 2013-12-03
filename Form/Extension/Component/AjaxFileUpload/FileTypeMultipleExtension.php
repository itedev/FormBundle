<?php

namespace ITE\FormBundle\Form\Extension\Component\AjaxFileUpload;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class FileTypeMultipleExtension
 * @package ITE\FormBundle\Form\Extension\Component\AjaxFileUpload
 */
class FileTypeMultipleExtension extends AbstractTypeExtension
{
    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'multiple' => false,
        ));
        $resolver->setAllowedTypes(array(
            'multiple' => 'bool',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars = array_replace($view->vars, array(
            'multiple' => $options['multiple']
        ));
        if ($options['multiple']) {
            $view->vars['attr']['multiple'] = 'multiple';
        }

        if ($options['multiple']) {
            $view->vars['full_name'] = $view->vars['full_name'] . '[]';
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getExtendedType()
    {
        return 'file';
    }
}