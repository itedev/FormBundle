<?php

namespace ITE\FormBundle\Form\Extension;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class EntityTypeSortExtension
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class EntityTypeSortExtension extends AbstractTypeExtension
{
    /**
     * {@inheritdoc}
     */
    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        if (!$options['sort']) {
            return;
        }

        $choices = $view->vars['choices'];
        ksort($choices);
        $view->vars['choices'] = $choices;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults([
            'sort' => false,
        ]);
        $resolver->setAllowedTypes([
            'sort' => ['bool'],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getExtendedType()
    {
        return 'entity';
    }
}
