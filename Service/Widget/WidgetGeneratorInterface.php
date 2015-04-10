<?php

namespace ITE\FormBundle\Service\Widget;

use Symfony\Component\Form\FormView;

/**
 * Interface WidgetGeneratorInterface
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
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