<?php

namespace ITE\FormBundle\SF;

/**
 * Class ElementTree
 * @package ITE\FormBundle\SF
 */
class ElementTree
{
    /**
     * @var Element[] $elements
     */
    protected $elements = array();

    /**
     * @param $selector
     * @return bool
     */
    public function has($selector)
    {
        return array_key_exists($selector, $this->elements);
    }

    /**
     * @param $element
     * @param array $parents
     * @param $options
     */
    public function add($element, array $parents, $options)
    {
        if ($this->has($element)) {
            return;
        }

        foreach ($parents as $parent) {
            if (!$this->has($parent)) {
                $this->elements[$parent] = new Element($parent);
            }
        }

        $this->elements[$element] = new Element($element, $parents, $options);
    }

    /**
     * @return array
     */
    public function peekAll()
    {
        return array_map(function(Element $element) {
            return array(
                'parents' => $element->getParents(),
                'options' => $element->getOptions(),
            );
        }, $this->elements);
    }

    /**
     * @return int
     */
    public function count()
    {
        return count($this->elements);
    }
} 