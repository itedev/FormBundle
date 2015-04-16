<?php

namespace ITE\FormBundle\SF;

/**
 * Class ElementBag
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class ElementBag
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
     * @param $selector
     * @param null $default
     * @return Element|null
     */
    public function get($selector, $default = null)
    {
        return $this->has($selector) ? $this->elements[$selector] : $default;
    }

    /**
     * @param $selector
     * @param array $options
     * @return Element
     */
    public function add($selector, $options = [])
    {
        if (!$this->has($selector)) {
            $this->elements[$selector] = new Element($selector, $options);
        }

        return $this->get($selector);
    }

    /**
     * @param string $selector
     * @param array $parentSelectors
     * @param array $options
     */
    public function addHierarchicalElement($selector, $parentSelectors, array $options = [])
    {
        $this->processSelector($selector, $options);
        if (null === $element = $this->get($selector)) {
            $element = $this->add($selector, $options);
        }

        foreach ($parentSelectors as $i => $parentSelector) {
            $parentOptions = array();
            $this->processSelector($parentSelector, $parentOptions);
            if (null === $parent = $this->get($parentSelector)) {
                $parent = $this->add($parentSelector, $parentOptions);
            }

            $element->addParent($parentSelector);
            $parent->addChild($selector);
        }
    }

    /**
     * @param $selector
     * @param $plugin
     * @param $pluginData
     */
    public function addPluginElement($selector, $plugin, $pluginData)
    {
        if (null === $element = $this->get($selector)) {
            $element = $this->add($selector);
        }
        $element->addPlugin($plugin, $pluginData);
    }

    /**
     * @return array
     */
    public function peekAll()
    {
        return array_map(function(Element $element) {
            return $element->getOptions();
        }, $this->elements);
    }

    /**
     * @return int
     */
    public function count()
    {
        return count($this->elements);
    }

    /**
     * @param $selector
     * @param array $options
     */
    protected function processSelector(&$selector, &$options = array())
    {
        if (false === strpos($selector, ' ')) {
            return;
        }
        list($selector, $childrenSelector) = explode(' ', $selector, 2);
        $options['delegate_selector'] = $childrenSelector;
    }
}