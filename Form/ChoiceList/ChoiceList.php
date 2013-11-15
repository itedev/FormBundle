<?php

namespace ITE\FormBundle\Form\ChoiceList;

use Symfony\Component\Form\Extension\Core\ChoiceList\ChoiceList as BaseChoiceList;

/**
 * Class ChoiceList
 * @package ITE\FormBundle\Form\ChoiceList
 */
class ChoiceList extends BaseChoiceList
{
    /**
     * @var bool $allowModify
     */
    protected $allowModify = false;

    /**
     * @var array $oldValues
     */
    protected $oldValues = array();

    /**
     * @var array $newValues
     */
    protected $newValues = array();

    /**
     * @var int $initialValueCount
     */
    protected $initialValueCount;

    /**
     * Set allowModify
     *
     * @param boolean $allowModify
     * @return ChoiceList
     */
    public function setAllowModify($allowModify)
    {
        $this->allowModify = $allowModify;

        return $this;
    }

    /**
     * Get allowModify
     *
     * @return boolean
     */
    public function getAllowModify()
    {
        return $this->allowModify;
    }

    /**
     * Get newValues
     *
     * @return array
     */
    public function getNewValues()
    {
        return $this->newValues;
    }

    /**
     * Get oldValues
     *
     * @return array
     */
    public function getOldValues()
    {
        return $this->oldValues;
    }

    /**
     * @param $data
     * @return $this
     */
    public function setNewValuesFromData($data)
    {
        $newValues = $this->getNewValuesFromData($data);

        $this->oldValues = $this->getValues();
        $this->newValues = $newValues;

        if (empty($newValues)) {
            return $this;
        }

        $choices = array_combine($newValues, $newValues);
        $bucketForPreferred = array();
        $bucketForRemaining = array();
        $this->addChoices($bucketForPreferred, $bucketForRemaining, $choices, $choices, array());

        return $this;
    }

    /**
     * @param $data
     * @return array
     */
    protected function getNewValuesFromData($data)
    {
        if (!is_array($data)) {
            $data = array($data);
        }

        $submittedValues = $this->fixValues($data);
        $existingValues = $this->getValues();
        $existingValueCount = count($existingValues);

        $newValues = array_diff($submittedValues, $existingValues);
        if (empty($newValues)) {
            return array();
        }

        $keys = range($existingValueCount, $existingValueCount + count($newValues) - 1);

        return array_combine($keys, $newValues);
    }
} 