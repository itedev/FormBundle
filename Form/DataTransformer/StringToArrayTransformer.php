<?php

namespace ITE\FormBundle\Form\DataTransformer;

use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Extension\Core\ChoiceList\ChoiceListInterface;

/**
 * Class StringToArrayTransformer
 * @package Symfony\Component\Form\Extension\Core\DataTransformer
 */
class StringToArrayTransformer implements DataTransformerInterface
{
//    private $choiceList;
//
//    /**
//     * @param ChoiceListInterface $choiceList
//     */
//    public function __construct(ChoiceListInterface $choiceList)
//    {
//        $this->choiceList = $choiceList;
//    }

    /**
     * @param mixed $values
     * @return mixed|string
     * @throws \Symfony\Component\Form\Exception\TransformationFailedException
     */
    public function transform($values)
    {
        if (null === $values) {
            return '';
        }

        if (!is_array($values)) {
            throw new TransformationFailedException('Expected an array.');
        }

        return implode(',', $values);
    }

    /**
     * @param mixed $values
     * @return array|mixed
     */
    public function reverseTransform($values)
    {
        if (null === $values) {
            return array();
        }

        return explode(',', $values[0]);
    }
}
