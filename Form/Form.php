<?php

namespace ITE\FormBundle\Form;

use ITE\Common\Util\ReflectionUtils;
use ITE\FormBundle\Util\FormUtils;
use ITE\FormBundle\Util\HierarchicalUtils;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Form\Form as BaseForm;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class Form
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class Form extends BaseForm implements FormInterface
{
    ///**
    // * @var array $options
    // */
    //protected $options = [];
    //
    ///**
    // * @return array
    // */
    //public function Options()
    //{
    //    return $this->options;
    //}
    //
    ///**
    // * @param string $name
    // * @return bool
    // */
    //public function hasOption($name)
    //{
    //    return array_key_exists($name, $this->options);
    //}
    //
    ///**
    // * @param string $name
    // * @param mixed $defaultValue
    // * @return mixed
    // */
    //public function getOption($name, $defaultValue = null)
    //{
    //    return $this->hasOption($name) ? $this->options[$name] : $defaultValue;
    //}
    //
    ///**
    // * @param string $name
    // * @param mixed $value
    // * @return $this
    // */
    //public function setOption($name, $value)
    //{
    //    $this->options[$name] = $value;
    //
    //    return $this;
    //}

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
    public function setRawOption($name, $value)
    {
        $config = $this->getConfig();

        $options = $config->getOptions();
        $options[$name] = $value;
        ReflectionUtils::setValue($config, 'options', $options);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function unsetRawOption($name)
    {
        $config = $this->getConfig();

        $options = $config->getOptions();
        if (array_key_exists($name, $options)) {
            unset($options[$name]);
        }
        ReflectionUtils::setValue($config, 'options', $options);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setRawAttribute($name, $value)
    {
        $config = $this->getConfig();

        $attributes = $config->getAttributes();
        $attributes[$name] = $value;
        ReflectionUtils::setValue($config, 'attributes', $attributes);

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
     * @return mixed
     */
    public function getOriginalData()
    {
        return $this->getConfig()->getOption('original_data');
    }

    /**
     * @param Request $request
     * @return bool
     */
    public function isHierarchicalOriginator(Request $request)
    {
        $fullName = FormUtils::getFullName($this);

        return HierarchicalUtils::isHierarchicalRequest($request)
        && in_array($fullName, HierarchicalUtils::getOriginators($request));
    }

    /**
     * {@inheritdoc}
     */
    public function add($child, $type = null, array $options = [])
    {
        /**
         * need same code as in \ITE\FormBundle\Form\Builder\FormBuilder::create() because hierarchical field can be
         * added right in form event (e.g. FormEvents::PRE_SET_DATA).
         */
        $originalOptions = $options;
        if (array_key_exists('skip_interceptors', $originalOptions)) {
            unset($originalOptions['skip_interceptors']);
        }
        if (array_key_exists('original_data', $originalOptions)) {
            unset($originalOptions['original_data']);
        }
        if (array_key_exists('hierarchical_data', $originalOptions)) {
            unset($originalOptions['hierarchical_data']);
        }
        // commented code below is redundant (ideally)
        //if (isset($originalOptions['original_type'])) {
        //    unset($originalOptions['original_type']);
        //}
        //if (isset($originalOptions['original_options'])) {
        //    unset($originalOptions['original_options']);
        //}

//        if ($this->getConfig()->getOption('skip_interceptors', false)) {
//            $options['skip_interceptors'] = true;
//        }
        $options = array_merge($options, [
            'original_type' => $type,
            'original_options' => $originalOptions,
        ]);

        return parent::add($child, $type, $options);
    }

    /**
     * {@inheritdoc}
     */
    public function addHierarchical($child, $parents, $type = null, array $options = [], $callback = null)
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
            $parents = [$parents];
        }
        if (!is_callable($callback)) {
            throw new \InvalidArgumentException('The form modifier handler must be a valid PHP callable.');
        }

        $options = array_merge($options, [
            'hierarchical_parents' => $parents,
            'hierarchical_callback' => $callback,
        ]);
        $this->add($child, $type, $options);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function replaceType($name, $type, $modifier = null)
    {
        $child = $this->get($name);
        $options = $child->getConfig()->getOption('original_options');

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
        $type = $child->getConfig()->getOption('original_type');
        $options = $child->getConfig()->getOption('original_options');

        if (is_callable($modifier)) {
            $options = call_user_func($modifier, $options);
        }

        return $this->add($name, $type, $options);
    }
}
