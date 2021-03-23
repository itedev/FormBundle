<?php

namespace ITE\FormBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\Form\Extension\Core\ChoiceList\ChoiceListInterface;

/**
 * Class DynamicChoicesToValuesTransformer
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class DynamicChoicesToValuesTransformer implements DataTransformerInterface
{
    /**
     * @var ChoiceListInterface
     */
    private $choiceList;

    /**
     * Constructor.
     *
     * @param ChoiceListInterface $choiceList
     */
    public function __construct(ChoiceListInterface $choiceList)
    {
        $this->choiceList = $choiceList;
    }

    /**
     * {@inheritDoc}
     */
    public function transform($array)
    {
        if (null === $array) {
            return [];
        }

        if (!is_array($array)) {
            throw new TransformationFailedException('Expected an array.');
        }

        return $this->choiceList->getValuesForChoices($array);
    }

    /**
     * {@inheritDoc}
     */
    public function reverseTransform($array)
    {
        if (null === $array) {
            return [];
        }

        if (!is_array($array)) {
            throw new TransformationFailedException('Expected an array.');
        }

        $choices = $this->choiceList->getChoicesForValues($array);
        if (count($choices) !== count($array)) {
            $this->choiceList->addDataChoices($array);

            $choices = $this->choiceList->getChoicesForValues($array);
            if (count($choices) !== count($array)) {
                throw new TransformationFailedException('Could not find all matching choices for the given values');
            }
        }

        return $choices;
    }
}
