<?php

namespace ITE\FormBundle\Form\Doctrine\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use ITE\FormBundle\Form\Doctrine\ChoiceList\AjaxEntityChoiceList;
use Symfony\Component\Routing\RouterInterface;

/**
 * Class AjaxEntityType
 * @package ITE\FormBundle\Form\Doctrine\Type
 */
class AjaxEntityType extends AbstractType
{
    /**
     * @var RouterInterface $router
     */
    protected $router;

    /**
     * @param RouterInterface $router
     */
    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    /**
     * Get router
     *
     * @return RouterInterface
     */
    public function getRouter()
    {
        return $this->router;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $type = $this;
        $url = function (Options $options) use ($type) {
            return $type->getRouter()->generate($options['route'], $options['route_parameters']);
        };

        $resolver->setDefaults(array(
            'em'               => null,
            'property'         => null,
            'query_builder'    => null,
            'choices'          => null,
            'group_by'         => null,
            'route_parameters' => array(),
            'url'              => $url,
            'choice_list'      => function (Options $options) {
                return new AjaxEntityChoiceList(
                    $options['em'],
                    $options['class'],
                    $options['property'],
                    $options['choices'],
                    $options['group_by']
                );
            },
        ));
        $resolver->setRequired(array(
            'route',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        /** @var $options['choice_list'] AjaxEntityChoiceList */
        $options['choice_list']->addEntities($form->getData());
        $view->vars = array_replace($view->vars, array(
            'choices' => $options['choice_list']->getRemainingViews(),
        ));
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
        return 'ite_ajax_entity';
    }
}