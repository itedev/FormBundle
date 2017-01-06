<?php

namespace ITE\FormBundle\Form\Builder\Event\Model;

use ArrayIterator;
use Countable;
use IteratorAggregate;
use Symfony\Component\Form\Util\FormUtil;

/**
 * Class HierarchicalParentCollection
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class HierarchicalParentCollection implements Countable, IteratorAggregate
{
    /**
     * @var array|HierarchicalParent[] $parents
     */
    private $parents = [];

    /**
     * @param array|HierarchicalParent[] $parents
     */
    public function __construct(array $parents = [])
    {
        $this->parents = $parents;
    }

    /**
     * @param string $parentName
     * @return bool
     */
    public function has($parentName)
    {
        return isset($this->parents[$parentName]) || array_key_exists($parentName, $this->parents);
    }

    /**
     * @param string $parentName
     * @return HierarchicalParent|null
     */
    public function get($parentName)
    {
        return $this->has($parentName) ? $this->parents[$parentName] : null;
    }

    /**
     * @return bool
     */
    public function isEmpty()
    {
        foreach ($this->parents as $parentName => $parent) {
            if (!$parent->isEmpty()) {
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
        foreach ($this->parents as $parentName => $parent) {
            if ($parent->isEmpty()) {
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
