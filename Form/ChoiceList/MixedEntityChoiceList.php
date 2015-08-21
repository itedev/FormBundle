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

//    /**
//     * @var bool
//     */
//    private $loaded = false;

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
        foreach ($this->entityChoiceLists as $alias => $entityChoiceList) {
            $choices = $entityChoiceList->getChoices();
            $a = 1;
        }
        $a = 1;
    }

    /**
     * {@inheritdoc}
     */
    public function getValues()
    {
        foreach ($this->entityChoiceLists as $alias => $entityChoiceList) {
            $values = $entityChoiceList->getValues();
            $a = 1;
        }
        $a = 1;
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
        $remainingViews = [];
        foreach ($this->entityChoiceLists as $alias => $entityChoiceList) {
            $entityPreferredViews = $entityChoiceList->getPreferredViews();
            $entityRemainingViews = $entityChoiceList->getRemainingViews();

            array_walk($entityPreferredViews, function(ChoiceView $choiceView) use ($alias) {
                $choiceView->value = sprintf('%s_%s', $alias, $choiceView->value);
            });
            array_walk($entityRemainingViews, function(ChoiceView $choiceView) use ($alias) {
                $choiceView->value = sprintf('%s_%s', $alias, $choiceView->value);
            });

            $label = $this->entityLabels[$alias];
            $remainingViews[$label] = array_merge($entityPreferredViews, $entityRemainingViews);
        }

        return $remainingViews;
    }

    /**
     * {@inheritdoc}
     */
    public function getChoicesForValues(array $values)
    {
        foreach ($this->entityChoiceLists as $alias => $entityChoiceList) {
            $choices = $entityChoiceList->getChoicesForValues($values);
            $a = 1;
        }

        return $choices;
    }

    /**
     * {@inheritdoc}
     */
    public function getValuesForChoices(array $choices)
    {
        foreach ($this->entityChoiceLists as $alias => $entityChoiceList) {
            $values = $entityChoiceList->getValuesForChoices($choices);
            $a = 1;
        }

        return $values;
    }

    /**
     * {@inheritdoc}
     */
    public function getIndicesForChoices(array $choices)
    {
        $a = 1;
    }

    /**
     * {@inheritdoc}
     */
    public function getIndicesForValues(array $values)
    {
        $a = 1;
    }
}