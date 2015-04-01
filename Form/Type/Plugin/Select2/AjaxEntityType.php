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
 * @package ITE\FormBundle\Form\Type\Plugin\Select2
 */
class AjaxEntityType extends AbstractAjaxChoiceType
{
    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        parent::setDefaultOptions($resolver);

        $self = $this;

        $createUrl = function (Options $options) use ($self) {
            if ($options['allow_create']) {
                if (isset($options['create_route'])) {
                    return $self->getRouter()->generate($options['create_route']);
                }

                throw new RuntimeException('You must specify create_route when using true for allow_create option.');
            }

            return null;
        };

        $resolver->setDefaults(array(
            'choice_list' => function (Options $options) {
                return new AjaxEntityChoiceList(
                    $options['em'],
                    $options['class'],
                    $options['property']
                );
            },
            'allow_create' => false,
            'create_url' => $createUrl,
        ));
        $resolver->setAllowedTypes(array(
            'allow_create' => array('bool'),
        ));
        $resolver->setOptional(array(
            'create_route',
        ));
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

        $extras =& $view->vars['plugins'][Select2Plugin::NAME]['extras'];
        $extras['allow_create'] = $options['allow_create'];
        $extras['create_url'] = $options['create_url'];
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