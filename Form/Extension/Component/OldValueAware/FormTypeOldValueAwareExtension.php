<?php

namespace ITE\FormBundle\Form\Extension\Component\OldValueAware;

use ITE\FormBundle\SF\Form\ClientFormTypeExtensionInterface;
use ITE\FormBundle\SF\Form\ClientFormView;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class FormTypeOldValueAwareExtension
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class FormTypeOldValueAwareExtension extends AbstractTypeExtension implements ClientFormTypeExtensionInterface
{
    /**
     * {@inheritdoc}
     */
    public function buildClientView(ClientFormView $clientView, FormView $view, FormInterface $form, array $options)
    {
        $isOldValueAware = isset($options['old_value_aware']) && $options['old_value_aware'];

        if ($isOldValueAware) {
            $clientView->setOption('old_value_aware', true);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setOptional([
            'old_value_aware',
        ]);
        $resolver->setAllowedTypes([
            'old_value_aware' => ['bool'],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getExtendedType()
    {
        return 'form';
    }
}
