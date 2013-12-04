<?php

namespace ITE\FormBundle\Form\Type\Component\DynamicChoice;

use ITE\FormBundle\Form\ChoiceList\AjaxEntityChoiceList;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\ChoiceList\ChoiceList;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Routing\RouterInterface;

/**
 * Class AjaxEntityType
 * @package ITE\FormBundle\Form\Type\Component\DynamicChoice
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
            'route_parameters' => array(),
            'url'              => $url,
            'choices'          => array(),
            'choice_list'      => function (Options $options) {
                    return new AjaxEntityChoiceList(
                        $options['em'],
                        $options['class'],
                        $options['property']
                    );
                },
            'allow_modify' => true,
        ));
        $resolver->setRequired(array(
            'route',
        ));
        $resolver->setAllowedValues(array(
            'allow_modify' => array(true),
            'choices' => array(array()),
            'expanded' => array(false),
        ));
    }

//    /**
//     * {@inheritdoc}
//     */
//    public function buildView(FormView $view, FormInterface $form, array $options)
//    {
//        $options['choice_list']->addEntities($form->getData());
//        $view->vars = array_replace($view->vars, array(
//            'choices' => $options['choice_list']->getRemainingViews(),
//        ));
//    }

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