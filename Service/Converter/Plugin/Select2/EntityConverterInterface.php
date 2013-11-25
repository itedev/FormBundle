<?php

namespace ITE\FormBundle\Service\Converter\Plugin\Select2;

use ITE\FormBundle\Service\Converter\EntityConverterInterface as BaseEntityConverterInterface;

/**
 * Interface EntityConverterInterface
 * @package ITE\FormBundle\Service\Converter\Plugin\Select2
 */
interface EntityConverterInterface extends BaseEntityConverterInterface
{
    /**
     * @param $choices
     * @return array
     */
    public function convertChoicesToOptions($choices);

    /**
     * @param $choice
     * @return array
     */
    public function convertChoiceToOption($choice);
}