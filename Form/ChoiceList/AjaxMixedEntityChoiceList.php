<?php

namespace ITE\FormBundle\Form\ChoiceList;

use Doctrine\Common\Util\ClassUtils;
use Doctrine\ORM\EntityManager;
use ITE\FormBundle\Util\MixedEntityUtils;
use Symfony\Component\Form\Extension\Core\ChoiceList\ObjectChoiceList;
use Symfony\Component\Form\Extension\Core\View\ChoiceView;

/**
 * Class AjaxMixedEntityChoiceList
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class AjaxMixedEntityChoiceList extends ObjectChoiceList
{
    /**
     * @var array
     */
    private $options;

    /**
     * @var array
     */
    private $aliases;

    /**
     * @var array|AjaxEntityChoiceList[]
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
     * @param array $options
     * @param array|AjaxEntityChoiceList[] $entityChoiceLists
     * @param array $entityLabels
     */
    public function __construct(array $options, array $entityChoiceLists, array $entityLabels)
    {
        foreach ($options as $alias => $entityOptions) {
            /** @var EntityManager $em */
            $em = $entityOptions['em'];
            $class = $entityOptions['class'];

            $classMetadata = $em->getClassMetadata($class);
            $class = $classMetadata->getName();

            $options[$alias]['class'] = $class;
            $options[$alias]['classMetadata'] = $classMetadata;

            $this->aliases[$class] = $alias;
        }
        $this->options = $options;
        $this->entityChoiceLists = $entityChoiceLists;
        $this->entityLabels = $entityLabels;
    }

    /**
     * @param mixed $data
     */
    public function setData($data)
    {
        if (!is_array($data) && !($data instanceof \Traversable)) {
            $data = [$data];
        }
        $dataByAlias = [];
        foreach ($data as $entity) {
            $class = ClassUtils::getRealClass(get_class($entity));
            $alias = $this->aliases[$class];

            if (!isset($dataByAlias[$alias])) {
                $dataByAlias[$alias] = [];
            }
            $dataByAlias[$alias][] = $entity;
        }

        foreach ($this->entityChoiceLists as $alias => $entityChoiceList) {
            if (isset($dataByAlias[$alias])) {
                $entityChoiceList->setData($dataByAlias[$alias]);
            }
        }

        $this->load();
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