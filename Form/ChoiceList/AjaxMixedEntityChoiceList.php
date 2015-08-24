<?php

namespace ITE\FormBundle\Form\ChoiceList;

use Symfony\Component\Form\Extension\Core\ChoiceList\ChoiceList as CoreChoiceList;
use Symfony\Bridge\Doctrine\Form\ChoiceList\EntityChoiceList as CoreEntityChoiceList;
use Symfony\Component\Form\Extension\Core\ChoiceList\ChoiceListInterface;

/**
 * Class AjaxMixedEntityChoiceList
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class AjaxMixedEntityChoiceList extends CoreChoiceList implements ChoiceListInterface
{
    /**
     * @var array
     */
    private $options;

    /**
     * @param array $options
     */
    public function __construct(array $options)
    {
        $this->options = $options;
    }

    /**
     * {@inheritdoc}
     */
    public function getChoices()
    {

    }

    /**
     * {@inheritdoc}
     */
    public function getValues()
    {

    }

    /**
     * {@inheritdoc}
     */
    public function getPreferredViews()
    {

    }

    /**
     * {@inheritdoc}
     */
    public function getRemainingViews()
    {

    }

    /**
     * {@inheritdoc}
     */
    public function getChoicesForValues(array $values)
    {

    }

    /**
     * {@inheritdoc}
     */
    public function getValuesForChoices(array $choices)
    {

    }

}