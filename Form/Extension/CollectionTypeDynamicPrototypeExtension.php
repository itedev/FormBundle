<?php

namespace ITE\FormBundle\Form\Extension;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class CollectionTypeDynamicPrototypeExtension
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class CollectionTypeDynamicPrototypeExtension extends AbstractTypeExtension
{
    /**
     * {@inheritdoc}
     */
    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        if (!is_callable($options['prototype_data']) || !$form->getConfig()->hasAttribute('prototype')) {
            return;
        }

        $data = call_user_func($options['prototype_data'], $form->getParent()->getData());

        /** @var FormInterface $oldPrototype */
        $oldPrototype = $form->getConfig()->getAttribute('prototype');
        $oldPrototypeOptions = $oldPrototype->getConfig()->getOptions();

        $factory = $form->getConfig()->getFormFactory();
        $prototype = $factory->createNamed($options['prototype_name'], $options['type'], $data, $oldPrototypeOptions);

        $view->vars['prototype'] = $prototype->createView($view);
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults([
          'prototype_data' => null,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getExtendedType()
    {
        return 'collection';
    }
}