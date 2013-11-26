<?php

namespace ITE\FormBundle\Service\Widget;

use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormView;

/**
 * Class WidgetGenerator
 * @package ITE\FormBundle\Service\Widget
 */
class WidgetGenerator implements WidgetGeneratorInterface
{
    /**
     * @var FormFactoryInterface
     */
    protected $formFactory;

    /**
     * @param FormFactoryInterface $formFactory
     */
    public function __construct(FormFactoryInterface $formFactory)
    {
        $this->formFactory = $formFactory;
    }

    /**
     * @param $fullName
     * @param $type
     * @param array $options
     * @return FormView
     * @throws \InvalidArgumentException
     */
    public function createView($fullName, $type, $options = array())
    {
        if (!preg_match_all('/[\w\-:]+/i', $fullName, $matches)) {
            throw new \InvalidArgumentException();
        }
        $names = $matches[0];

        $name = array_pop($names);
        $parentView = null;
        foreach ($names as $parentName) {
            $parentView = $this->formFactory->createNamed($parentName, 'hidden', null, array(
                'mapped' => false,
                'csrf_protection' => false,
            ))->createView($parentView);
        }
        $view = $this->formFactory->createNamed($name, $type, null, array_replace($options, array(
            'mapped' => false,
            'csrf_protection' => false,
        )))->createView($parentView);

        if ('[]' === substr($fullName, -2)) {
            $view->vars['full_name'] .= '[]';
        }

        return $view;
    }

    /**
     * @param $fullName
     * @param array $choices
     * @param array $options
     * @return FormView
     */
    public function createChoiceView($fullName, array $choices, $options = array())
    {
        $options = array_replace($options, array(
            'choices' => $choices,
            'expanded' => true,
            'multiple' => '[]' === substr($fullName, -2) ? true : false,
        ));

        return $this->createView($fullName, 'choice', $options);
    }
} 