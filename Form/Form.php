<?php

namespace ITE\FormBundle\Form;

use ITE\Common\Util\ReflectionUtils;
use ITE\FormBundle\Form\Builder\FormBuilder;
use ITE\FormBundle\Form\EventListener\Component\Hierarchical\HierarchicalAddChildSubscriber;
use ITE\FormBundle\Form\EventListener\Component\Hierarchical\HierarchicalSetDataSubscriber;
use ITE\FormBundle\Util\FormUtils;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\Form as BaseForm;

/**
 * Class Form
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class Form extends BaseForm implements FormInterface
{
    /**
     * @var bool $lockSetData2
     */
    private $lockSetData2 = false;

    /**
     * {@inheritdoc}
     */
    public function getRawModelData()
    {
        return ReflectionUtils::getValue($this, 'modelData');
    }

    /**
     * {@inheritdoc}
     */
    public function setRawModelData($modelData)
    {
        ReflectionUtils::setValue($this, 'modelData', $modelData);
        
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getRawNormData()
    {
        return ReflectionUtils::getValue($this, 'normData');
    }

    /**
     * {@inheritdoc}
     */
    public function setRawNormData($normData)
    {
        ReflectionUtils::setValue($this, 'normData', $normData);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getRawViewData()
    {
        return ReflectionUtils::getValue($this, 'viewData');
    }

    /**
     * {@inheritdoc}
     */
    public function setRawViewData($viewData)
    {
        ReflectionUtils::setValue($this, 'viewData', $viewData);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getRawChildren()
    {
        return ReflectionUtils::getValue($this, 'children');
    }

    /**
     * {@inheritdoc}
     */
    public function setRawChildren($children)
    {
        ReflectionUtils::setValue($this, 'children', $children);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setRawParent(FormInterface $parent = null)
    {
        ReflectionUtils::setValue($this, 'parent', $parent);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setRawSubmitted($submitted)
    {
        ReflectionUtils::setValue($this, 'submitted', $submitted);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setRawOption($optionName, $optionValue)
    {
        $config = $this->getConfig();

        $options = $config->getOptions();
        $options[$optionName] = $optionValue;
        ReflectionUtils::setValue($config, 'options', $options);

        return $this;
    }
    //
    ///**
    // * {@inheritdoc}
    // */
    //public function setRawEventDispatcher(EventDispatcherInterface $ed)
    //{
    //    $config = $this->getConfig();
    //
    //    $refClass = new \ReflectionClass($config);
    //    while (!$refClass->hasProperty('dispatcher')) {
    //        $refClass = $refClass->getParentClass();
    //    }
    //    $refProp = $refClass->getProperty('dispatcher');
    //    $refProp->setAccessible(true);
    //    $refProp->setValue($config, $ed);
    //    $refProp->setAccessible(false);
    //
    //    return $this;
    //}

    /**
     * {@inheritdoc}
     */
    public function addHierarchical($child, $parents, $type = null, array $options = [], $formModifier = null)
    {
        if (!is_string($child)) {
            throw new UnexpectedTypeException($child, 'string');
        }
        if (!is_string($parents) && !is_array($parents)) {
            throw new UnexpectedTypeException($parents, 'string or array');
        }
        if (empty($parents)) {
            throw new \InvalidArgumentException('You must set at least one parent');
        }
        if (!is_array($parents)) {
            $parents = array($parents);
        }
        if (!is_callable($formModifier)) {
            throw new \InvalidArgumentException('The form modifier handler must be a valid PHP callable.');
        }

        $options = array_merge($options, [
            'hierarchical_parents' => $parents,
        ]);

        /** @var FormBuilder $config */
        $config = $this->getConfig();
        $formAccessor = $config->getFormAccessor();

        parent::add($child, $type, $options);

        FormUtils::addEventSubscriber($this->get($child), new HierarchicalSetDataSubscriber());

        // evaluate reference point
        $referenceLevelUp = false;
        $reference = FormUtils::getFormReference($this, $child, $referenceLevelUp);
        FormUtils::addEventSubscriber($reference, new HierarchicalAddChildSubscriber(
            $child,
            $type,
            $options,
            $parents,
            $formModifier,
            $referenceLevelUp,
            $formAccessor
        ));

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function replaceType($name, $type, $modifier = null)
    {
        $child = $this->get($name);
        $options = $child->getConfig()->getOptions();

        if (is_callable($modifier)) {
            $options = call_user_func($modifier, $options);
        }

        return $this->add($name, $type, $options);
    }

    /**
     * {@inheritdoc}
     */
    public function replaceOptions($name, $modifier)
    {
        $child = $this->get($name);
        $options = $child->getConfig()->getOptions();
        $type = $child->getConfig()->getType()->getName();

        if (is_callable($modifier)) {
            $options = call_user_func($modifier, $options);
        }

        return $this->add($name, $type, $options);
    }
}
