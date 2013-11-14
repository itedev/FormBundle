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

} 