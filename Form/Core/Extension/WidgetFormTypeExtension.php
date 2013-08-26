<?php

namespace ITE\FormBundle\Form\Core\Extension;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\AbstractTypeExtension;

class WidgetFormTypeExtension extends AbstractTypeExtension
{
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                'widget_addon' => array(
                    'type' => null, //false: dont add anything, null: using presets, anything; prepend; append
                    'icon' => null,
                    'text' => null,
                    'button' => false
                ),
            )
        );
    }

    public function getExtendedType()
    {
        return 'form';
    }
}
