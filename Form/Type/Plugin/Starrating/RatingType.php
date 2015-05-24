<?php

namespace ITE\FormBundle\Form\Type\Plugin\Starrating;

use ITE\FormBundle\Form\Type\Plugin\AbstractPluginType;
use ITE\FormBundle\SF\Plugin\StarratingPlugin;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class RatingType
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class RatingType extends AbstractPluginType
{
    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        parent::setDefaultOptions($resolver);

        $resolver->setDefaults([
            'expanded' => true,
        ]);
        $resolver->setAllowedValues([
            'expanded' => [true],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['plugins'][StarratingPlugin::getName()] = [
            'extras' => (object) [],
            'options' => (object) array_replace_recursive($this->options, $options['plugin_options'])
        ];
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
        return 'ite_starrating_rating';
    }
}