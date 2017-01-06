<?php

namespace ITE\FormBundle\Form\Type\Plugin\Select2;

use ITE\FormBundle\Form\Type\Plugin\Core\AbstractPluginType;
use ITE\FormBundle\SF\Form\ClientFormTypeInterface;
use ITE\FormBundle\SF\Form\ClientFormView;
use ITE\FormBundle\SF\Plugin\Select2Plugin;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class AjaxMixedEntityType
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class AjaxMixedEntityType extends AbstractAjaxChoiceType
{
    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        array_splice(
            $view->vars['block_prefixes'],
            array_search($this->getName(), $view->vars['block_prefixes']),
            0,
            'ite_select2'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'ite_ajax_mixed_entity';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'ite_select2_ajax_mixed_entity';
    }
}
