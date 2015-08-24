<?php

namespace ITE\FormBundle\Form\ChoiceList;

use ITE\FormBundle\Util\MixedEntityUtils;
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
            $unwrappedValues = MixedEntityUtils::unwrapValues($values, $alias);

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
            $wrappedValues = MixedEntityUtils::wrapValues($unwrappedValues, $alias);

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

            $wrappedChoices = MixedEntityUtils::wrapIndices($unwrappedChoices, $alias);
            $wrappedValues = MixedEntityUtils::wrapValues($unwrappedValues, $alias, true);
            $wrappedPreferredViews = MixedEntityUtils::wrapChoiceViews($unwrappedPreferredViews, $alias, true);
            $wrappedRemainingViews = MixedEntityUtils::wrapChoiceViews($unwrappedRemainingViews, $alias, true);

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

}