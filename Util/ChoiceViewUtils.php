<?php

namespace ITE\FormBundle\Util;

use Symfony\Component\Form\Extension\Core\View\ChoiceView;

/**
 * Class ChoiceViewUtils
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class ChoiceViewUtils
{
    /**
     * @param array|ChoiceView[] $choiceViews
     * @return array
     */
    public static function choiceViewsToChoices(array $choiceViews)
    {
        $choices = [];
        foreach ($choiceViews as $choiceView) {
            $choices[$choiceView->value] = $choiceView->label;
        }

        return $choices;
    }
}
