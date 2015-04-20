<?php

namespace ITE\FormBundle\Form\Builder\Event;

use ArrayIterator;
use Countable;
use IteratorAggregate;
use Symfony\Component\Form\Util\FormUtil;

/**
 * Class ParentCollection
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class ParentCollection implements Countable, IteratorAggregate
{
    /**
     * @var array $parents
     */
    private $parents = [];

    /**
     * @param array $parents
     */
    public function __construct(array $parents = [])
    {
        $this->parents = $parents;
    }

    /**
     * @param $parent
     * @return bool
     */
    public function has($parent)
    {
        return isset($this->parents[$parent]) || array_key_exists($parent, $this->parents);
    }

    /**
     * @param $parent
     * @return null
     */
    public function get($parent)
    {
        return $this->has($parent) ? $this->parents[$parent] : null;
    }

    /**
     * @return bool
     */
    public function isEmpty()
    {
        foreach ($this->parents as $parent => $parentData) {
            if (!FormUtil::isEmpty($parentData)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @return bool
     */
    public function isNotEmpty()
    {
        foreach ($this->parents as $parent => $parentData) {
            if (FormUtil::isEmpty($parentData)) {
                return false;
            }
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getIterator()
    {
        return new ArrayIterator($this->parents);
    }

    /**
     * {@inheritdoc}
     */
    public function count()
    {
        return count($this->parents);
    }

}