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
     * @var bool $allowAdd
     */
    protected $allowAdd = false;

    /**
     * Set allowAdd
     *
     * @param bool $allowAdd
     * @return ChoiceList
     */
    public function setAllowAdd($allowAdd)
    {
        $this->allowAdd = $allowAdd;

        return $this;
    }

    /**
     * Get allowAdd
     *
     * @return boolean
     */
    public function getAllowAdd()
    {
        return $this->allowAdd;
    }

} 