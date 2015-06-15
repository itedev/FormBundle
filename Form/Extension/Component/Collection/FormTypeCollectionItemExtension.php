<?php

namespace ITE\FormBundle\Form\Extension\Component\Collection;

use ITE\FormBundle\Util\FormUtils;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

/**
 * Class FormTypeCollectionItemExtension
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class FormTypeCollectionItemExtension extends AbstractTypeExtension
{
    /**
     * {@inheritdoc}
     */
    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        $parentView = $view->parent;
        if (null !== $parentView && FormUtils::isFormViewContainBlockPrefix($parentView, 'collection')) {
            $id = isset($view->vars['attr']['id']) ? $view->vars['attr']['id'] : $view->vars['id'];
            $newId = $id . '_item';

            $view->vars['id'] = $newId;
            $view->vars['attr']['id'] = $newId;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getExtendedType()
    {
        return 'form';
    }
}