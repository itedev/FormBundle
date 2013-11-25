<?php

namespace ITE\FormBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;

/**
 * Class StringToArrayTransformer
 * @package Symfony\Component\Form\Extension\Core\DataTransformer
 */
class StringToArrayTransformer implements DataTransformerInterface
{
    /**
     * @var string $separator
     */
    private $separator;

    /**
     * @param string $separator
     */
    public function __construct($separator = ',')
    {
        $this->separator = $separator;
    }

    /**
     * @param mixed $values
     * @return string
     */
    public function transform($values)
    {
        return implode($this->separator, $values);
    }

    /**
     * @param mixed $values
     * @return array|mixed
     */
    public function reverseTransform($values)
    {
        return $values;
    }
}
