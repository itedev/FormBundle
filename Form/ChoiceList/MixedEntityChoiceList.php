<?php

namespace ITE\FormBundle\Form\ChoiceList;

use Symfony\Component\Form\Extension\Core\ChoiceList\ChoiceList as CoreChoiceList;
use Symfony\Bridge\Doctrine\Form\ChoiceList\EntityChoiceList as CoreEntityChoiceList;
use Symfony\Component\Form\Extension\Core\ChoiceList\ChoiceListInterface;
use Symfony\Component\Form\Extension\Core\View\ChoiceView;

/**
 * Class MixedEntityChoiceList
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class MixedEntityChoiceList extends CoreChoiceList implements ChoiceListInterface
{
    /**
     * @var array
     */
    private $entityChoiceLists;

    /**
     * @var array
     */
    private $entityLabels;

    /**
     * @var array|ChoiceView[]
     */
    private $remainingViews = [];

    /**
     * @var bool
     */
    private $loaded = false;

    /**
     * @param array|CoreEntityChoiceList[] $entityChoiceLists
     * @param array $entityLabels
     */
    public function __construct(array $entityChoiceLists, array $entityLabels)
    {
        $this->entityChoiceLists = $entityChoiceLists;
        $this->entityLabels = $entityLabels;
    }

    /**
     * {@inheritdoc}
     */
    public function getChoices()
    {
        if (!$this->loaded) {
            $this->load();
        }

        return parent::getChoices();
    }

    /**
     * {@inheritdoc}
     */
    public function getValues()
    {
        if (!$this->loaded) {
            $this->load();
        }

        return parent::getValues();
    }

    /**
     * {@inheritdoc}
     */
    public function getPreferredViews()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getRemainingViews()
    {
        if (!$this->loaded) {
            $this->load();
        }

        return $this->remainingViews;
    }

    /**
     * {@inheritdoc}
     */
    public function getChoicesForValues(array $values)
    {
        $choices = [];
        foreach ($this->entityChoiceLists as $alias => $entityChoiceList) {
            $unwrappedValues = $this->unwrapValues($values, $alias);

            $choices = array_merge($choices, $entityChoiceList->getChoicesForValues($unwrappedValues));
        }

        return $choices;
    }

    /**
     * {@inheritdoc}
     */
    public function getValuesForChoices(array $choices)
    {
        $values = [];
        foreach ($this->entityChoiceLists as $alias => $entityChoiceList) {
            $unwrappedValues = $entityChoiceList->getValuesForChoices($choices);
            $wrappedValues = $this->wrapValues($unwrappedValues, $alias);

            $values = array_merge($values, $wrappedValues);
        }

        return $values;
    }

    private function load()
    {
        $choices = [];
        $values = [];
        $remainingViews = [];
        foreach ($this->entityChoiceLists as $alias => $entityChoiceList) {
            $unwrappedChoices = $entityChoiceList->getChoices();
            $unwrappedValues = $entityChoiceList->getValues();
            $unwrappedPreferredViews = $entityChoiceList->getPreferredViews();
            $unwrappedRemainingViews = $entityChoiceList->getRemainingViews();

            $wrappedChoices = $this->wrapIndices($unwrappedChoices, $alias);
            $wrappedValues = $this->wrapValues($unwrappedValues, $alias, true);
            $wrappedPreferredViews = $this->wrapChoiceViews($unwrappedPreferredViews, $alias, true);
            $wrappedRemainingViews = $this->wrapChoiceViews($unwrappedRemainingViews, $alias, true);

            $choices = array_merge($choices, $wrappedChoices);
            $values = array_merge($values, $wrappedValues);
            $label = $this->entityLabels[$alias];
            $remainingViews[$label] = array_merge($wrappedPreferredViews, $wrappedRemainingViews);
        }

        $this->choices = $choices;
        $this->values = $values;
        $this->remainingViews = $remainingViews;

        $this->loaded = true;
    }

    /**
     * @param array $values
     * @param string $alias
     * @return array
     */
    private function unwrapValues(array $values, $alias)
    {
        $unwrappedValues = [];
        foreach ($values as $value) {
            if (preg_match(sprintf('~^%s_(.+)$~', preg_quote($alias, '~')), $value, $matches)) {
                $unwrappedValues[] = $matches[1];
            }
        }

        return $unwrappedValues;
    }

    /**
     * @param array $array
     * @param $alias
     * @return array
     */
    private function wrapIndices(array $array, $alias)
    {
        $wrappedArray = [];
        foreach ($array as $index => $value) {
            $wrappedIndex = sprintf('%s_%s', $alias, $index);
            $wrappedArray[$wrappedIndex] = $value;
        }

        return $wrappedArray;
    }

    /**
     * @param array $values
     * @param $alias
     * @param bool $withIndices
     * @return array
     */
    private function wrapValues(array $values, $alias, $withIndices = false)
    {
        $wrappedValues = array_map(function($value) use ($alias) {
            return sprintf('%s_%s', $alias, $value);
        }, $values);

        if ($withIndices) {
            return $this->wrapIndices($wrappedValues, $alias);
        }

        return $wrappedValues;
    }

    /**
     * @param array $choiceViews
     * @param $alias
     * @param bool $withIndices
     * @return array
     */
    private function wrapChoiceViews(array $choiceViews, $alias, $withIndices = false)
    {
        $wrappedChoiceViews = array_map(function($choiceView) use ($alias) {
            $wrappedValue = sprintf('%s_%s', $alias, $choiceView->value);

            return new ChoiceView($choiceView->data, $wrappedValue, $choiceView->label);
        }, $choiceViews);

        if ($withIndices) {
            return $this->wrapIndices($wrappedChoiceViews, $alias);
        }

        return $wrappedChoiceViews;
    }
}