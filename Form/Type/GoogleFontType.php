<?php

namespace ITE\FormBundle\Form\Type;

use ITE\FormBundle\Form\ChoiceList\GoogleFontChoiceBuilder;
use Symfony\Component\Form\AbstractType as BaseAbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
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
            'choices' => GoogleFontChoiceBuilder::getChoices(),
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