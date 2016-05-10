<?php

namespace ITE\FormBundle\Form\Extension;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class FormTypeDefaultDataExtension
 *
 * @author sam0delkin <t.samodelkin@gmail.com>
 */
class FormTypeDefaultDataExtension extends AbstractTypeExtension
{
    /**
     * @inheritDoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if (isset($options['default_data'])) {
            $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
                $defaultData = $event->getForm()->getConfig()->getOption('default_data');
                if (null === $event->getData()) {
                    $event->setData($defaultData);
                }
            });
        }
    }

    /**
     * @inheritDoc
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setOptional([
            'default_data'
        ]);
    }

    /**
     * @inheritDoc
     */
    public function getExtendedType()
    {
        return 'form';
    }
}
