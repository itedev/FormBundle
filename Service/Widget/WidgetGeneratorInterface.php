<?php

namespace ITE\FormBundle\Service\Widget;

use Symfony\Component\Form\FormView;

/**
 * Interface WidgetGeneratorInterface
 * @package ITE\FormBundle\Service\Widget
 */
interface WidgetGeneratorInterface
{
    /**
     * @param $fullName
     * @param $type
     * @param array $options
     * @return FormView
     * @throws \InvalidArgumentException
     */
    public function createView($fullName, $type, $options = array());

    /**
     * @param $fullName
     * @param array $choices
     * @param array $options
     * @return FormView
     */
    public function createChoiceView($fullName, array $choices, $options = array());
} 