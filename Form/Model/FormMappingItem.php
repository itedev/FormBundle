<?php

namespace ITE\FormBundle\Form\Model;

use ITE\FormBundle\Util\FormUtils;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\PropertyAccess\PropertyPathInterface;

/**
 * Class FormMappingItem
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class FormMappingItem
{
    const PROTOTYPE_NAME = '__name__';

    /**
     * @var string
     */
    private $name;

    /**
     * @var array array
     */
    private $options = [];

    /**
     * @var array|FormMappingItem[]
     */
    private $children = [];

    /**
     * FormItem constructor.
     *
     * @param string $name
     * @param array $options
     */
    public function __construct($name = null, array $options = [])
    {
        $this->name = $name ?: 'form';
        $this->options = array_merge([
            'by_reference' => true,
            'property_path' => null,
            'mapped' => true
        ], $options);
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return FormMappingItem
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get options
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Set options
     *
     * @param array $options
     * @return FormMappingItem
     */
    public function setOptions(array $options)
    {
        $this->options = $options;

        return $this;
    }

    /**
     * Get children
     *
     * @return array|FormMappingItem[]
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * Set children
     *
     * @param array|FormMappingItem[] $children
     * @return FormMappingItem
     */
    public function setChildren(array $children)
    {
        $this->children = $children;

        return $this;
    }

    /**
     * @return bool
     */
    public function hasChildren()
    {
        return !empty($this->children);
    }

    /**
     * @param FormMappingItem $child
     * @return $this
     */
    public function addChild(FormMappingItem $child)
    {
        $this->children[$child->getName()] = $child;
        
        return $this;
    }

    /**
     * @param string $name
     * @return FormMappingItem|null
     */
    public function getChild($name)
    {
        return $this->hasChild($name) ? $this->children[$name] : null;
    }

    /**
     * @param string $name
     * @return bool
     */
    public function hasChild($name)
    {
        return array_key_exists($name, $this->children);
    }

    /**
     * @param string $name
     * @return FormMappingItem
     */
    public function removeChild($name)
    {
        if ($this->hasChild($name)) {
            unset($this->children[$name]);
        }
        
        return $this;
    }

    /**
     * @param string $name
     * @return bool
     */
    public function hasOption($name)
    {
        return array_key_exists($name, $this->options);
    }

    /**
     * @param string $name
     * @param mixed $defaultValue
     * @return mixed
     */
    public function getOption($name, $defaultValue = null)
    {
        return $this->hasOption($name) ? $this->options[$name] : $defaultValue;
    }

    /**
     * @return bool
     */
    public function getByReference()
    {
        return $this->getOption('by_reference', true);
    }

    /**
     * @return bool
     */
    public function getMapped()
    {
        return $this->getOption('mapped', true);
    }

    /**
     * @return null|string|PropertyPathInterface
     */
    public function getPropertyPath()
    {
        return $this->getOption('property_path');
    }

    /**
     * @return bool
     */
    public function isCollection()
    {
        return $this->hasChild(self::PROTOTYPE_NAME);
    }

    /**
     * @return bool
     */
    public function isCollectionItem()
    {
        return self::PROTOTYPE_NAME === $this->getName();
    }

    /**
     * @return FormMappingItem|null
     */
    public function getPrototype()
    {
        return $this->getChild(self::PROTOTYPE_NAME);
    }

    ///

    /**
     * @param FormInterface $prototype
     * @return FormMappingItem
     */
    public static function createFromPrototype(FormInterface $prototype)
    {
        $rootItem = new FormMappingItem($prototype->getName(), [
            'by_reference' => $prototype->getConfig()->getByReference(),
            'mapped' => $prototype->getConfig()->getMapped() && !$prototype->getConfig()->getDisabled(),
            'property_path' => $prototype->getPropertyPath(),
        ]);

        FormUtils::formWalkRecursiveWithPrototype($prototype, function (
            FormInterface $child,
            FormInterface $parent,
            FormMappingItem $parentItem
        ) {
            $childName = $child->getName();
            if ($parent->getConfig()->getAttribute('prototype')) {
                $childName = self::PROTOTYPE_NAME;
            }

            $childItem = new FormMappingItem($childName, [
                'by_reference' => $child->getConfig()->getByReference(),
                'mapped' => $child->getConfig()->getMapped() && !$child->getConfig()->getDisabled(),
                'property_path' => $child->getPropertyPath(),
            ]);

            $parentItem->addChild($childItem);

            return [
                'form' => $child,
                'mapping' => $childItem,
            ];
        }, [
            'form' => $prototype,
            'mapping' => $rootItem,
        ]);

        return $rootItem;
    }

    /**
     * @param array $mapping
     * @return FormMappingItem
     */
    public static function createFromArray(array $mapping)
    {
        $rootItem = new FormMappingItem();

        foreach ($mapping as $name => $options) {
            self::processChildFromArray($rootItem, $name, $options);
        }

        return $rootItem;
    }

    /**
     * @param FormMappingItem $parentItem
     * @param mixed $name
     * @param mixed $options
     */
    private static function processChildFromArray(FormMappingItem $parentItem, $name, $options)
    {
        $children = [];
        if (is_string($options)) {
            $name = $options;
            $options = [];
        } elseif (is_array($options)) {
            if (array_key_exists('children', $options)) {
                $children = $options['children'];
                unset($options['children']);
            }
        }

        $childItem = new FormMappingItem($name, $options);
        $parentItem->addChild($childItem);

        foreach ($children as $childName => $childOptions) {
            self::processChildFromArray($childItem, $childName, $childOptions);
        }
    }
}
