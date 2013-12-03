<?php

namespace ITE\FormBundle\Form\Extension\Component\Collection;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class CollectionTypeCollectionExtension
 * @package ITE\FormBundle\Form\Extension\Component\Collection
 */
class CollectionTypeCollectionExtension extends AbstractTypeExtension
{
    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'collection_id' => null,
            'collection_item_tag' => 'div'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['collection_id'] = $options['collection_id'];
        $view->vars['collection_item_tag'] = $options['collection_item_tag'];
    }

    /**
     * {@inheritdoc}
     */
    public function getExtendedType()
    {
        return 'collection';
    }
}