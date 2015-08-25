<?php

namespace ITE\FormBundle\Form\Type\Plugin\Select2;

use ITE\FormBundle\SF\Plugin\Select2Plugin;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Routing\RouterInterface;

/**
 * Class AjaxChoiceType
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class AjaxChoiceType extends AbstractAjaxChoiceType
{
    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'ite_ajax_choice';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'ite_select2_ajax_choice';
    }
} 