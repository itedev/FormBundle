<?php

namespace ITE\FormBundle\Form\Type;

use ITE\FormBundle\Form\ChoiceList\Builder\GoogleFontChoiceListBuilder;
use Symfony\Component\Form\AbstractType as BaseAbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class GoogleFontType
 * @package ITE\FormBundle\Form\Type
 */
class GoogleFontType extends BaseAbstractType
{
    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'choices' => GoogleFontChoiceListBuilder::getChoices(),
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'choice';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'ite_google_font';
    }
}