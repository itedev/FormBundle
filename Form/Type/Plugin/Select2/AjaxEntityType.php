<?php

namespace ITE\FormBundle\Form\Type\Plugin\Select2;

use ITE\FormBundle\Form\ChoiceList\AjaxEntityChoiceList;
use ITE\FormBundle\SF\Plugin\Select2Plugin;
use RuntimeException;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Routing\RouterInterface;

/**
 * Class AjaxEntityType
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class AjaxEntityType extends AbstractAjaxChoiceType
{
    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        parent::setDefaultOptions($resolver);

        $resolver->setDefaults([
            'choice_list' => function (Options $options) {
                return new AjaxEntityChoiceList(
                    $options['em'],
                    $options['class'],
                    $options['property']
                );
            },
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        parent::buildView($view, $form, $options);

        $view->vars['attr']['data-property'] = $options['property'];
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'entity';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'ite_select2_ajax_entity';
    }
}