<?php

namespace ITE\FormBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\Form\Extension\Core\ChoiceList\ChoiceListInterface;

/**
 * Class DynamicChoiceToValueTransformer
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class DynamicChoiceToValueTransformer implements DataTransformerInterface
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
    public function transform($choice)
    {
        return (string) current($this->choiceList->getValuesForChoices([$choice]));
    }

    /**
     * {@inheritDoc}
     */
    public function reverseTransform($value)
    {
        if (null !== $value && !is_scalar($value)) {
            throw new TransformationFailedException('Expected a scalar.');
        }

        // These are now valid ChoiceList values, so we can return null
        // right away
        if ('' === $value || null === $value) {
            return;
        }

        $choices = $this->choiceList->getChoicesForValues([$value]);
        if (1 !== count($choices)) {
            $this->choiceList->setData($value);

            $choices = $this->choiceList->getChoicesForValues([$value]);
            if (1 !== count($choices)) {
                throw new TransformationFailedException(
                    sprintf('The choice "%s" does not exist or is not unique', $value)
                );
            }
        }

        $choice = current($choices);

        return '' === $choice ? null : $choice;
    }
}
