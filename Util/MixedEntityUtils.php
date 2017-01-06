<?php

namespace ITE\FormBundle\Util;

use Symfony\Component\Form\Extension\Core\View\ChoiceView;

/**
 * Class MixedEntityUtils
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class MixedEntityUtils
{
    /**
     * @param string $value
     * @param string $alias
     * @return string
     */
    public static function wrapValue($value, $alias)
    {
        return sprintf('%s_%s', $alias, $value);
    }

    /**
     * @param array $array
     * @param string $alias
     * @return array
     */
    public static function wrapIndices(array $array, $alias)
    {
        $wrappedArray = [];
        foreach ($array as $index => $value) {
            $wrappedIndex = self::wrapValue($index, $alias);
            $wrappedArray[$wrappedIndex] = $value;
        }

        return $wrappedArray;
    }

    /**
     * @param array $values
     * @param string $alias
     * @param bool $withIndices
     * @return array
     */
    public static function wrapValues(array $values, $alias, $withIndices = false)
    {
        $wrappedValues = array_map(function ($value) use ($alias) {
            return self::wrapValue($value, $alias);
        }, $values);

        if ($withIndices) {
            return self::wrapIndices($wrappedValues, $alias);
        }

        return $wrappedValues;
    }

    /**
     * @param array $choiceViews
     * @param string $alias
     * @param bool $withIndices
     * @return array
     */
    public static function wrapChoiceViews(array $choiceViews, $alias, $withIndices = false)
    {
        $wrappedChoiceViews = array_map(function ($choiceView) use ($alias) {
            $wrappedValue = self::wrapValue($choiceView->value, $alias);

            return new ChoiceView($choiceView->data, $wrappedValue, $choiceView->label);
        }, $choiceViews);

        if ($withIndices) {
            return self::wrapIndices($wrappedChoiceViews, $alias);
        }

        return $wrappedChoiceViews;
    }

    /**
     * @param array $values
     * @param string $alias
     * @return array
     */
    public static function unwrapValues(array $values, $alias)
    {
        $unwrappedValues = [];
        foreach ($values as $value) {
            if (preg_match(sprintf('~^%s_(.+)$~', preg_quote($alias, '~')), $value, $matches)) {
                $unwrappedValues[] = $matches[1];
            }
        }

        return $unwrappedValues;
    }
}
