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
    /**
     * @var array $parameters
     */
    protected $parameters = [];

    /**
     * {@inheritdoc}
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * {@inheritdoc}
     */
    public function hasParameter($name)
    {
        return array_key_exists($name, $this->parameters);
    }

    /**
     * {@inheritdoc}
     */
    public function getParameter($name, $defaultValue = null)
    {
        return $this->hasParameter($name) ? $this->parameters[$name] : $defaultValue;
    }

    /**
     * {@inheritdoc}
     */
    public function setParameters(array $parameters)
    {
        $this->parameters = $parameters;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setParameter($name, $value)
    {
        $this->parameters[$name] = $value;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function addParameters(array $parameters)
    {
        $this->parameters = array_merge($this->parameters, $parameters);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function unsetParameter($name)
    {
        if ($this->hasParameter($name)) {
            unset($this->parameters);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function isHierarchicalAffected()
    {
        return $this->getParameter('hierarchical_affected', false);
    }

    ///

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

//    /**
//     * @param Request $request
//     * @return bool
//     */
//    public function isHierarchicalOriginator(Request $request)
//    {
//        $fullName = FormUtils::getFullName($this);
//
//        return HierarchicalUtils::isHierarchicalRequest($request)
//            && in_array($fullName, HierarchicalUtils::getOriginators($request));
//    }

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
        /**
         * no need to unset:
         * - original_type (anyway will be overwritten below)
         * - original_options (anyway will be overwritten below)
         */

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
        $options = $child->getConfig()->getOriginalOptions();

        if (is_callable($modifier)) {
            $options = call_user_func($modifier, $options);
        }

        return $this->add($name, $type, $options);
    }

    /**
     * {@inheritdoc}
     */
    public function replaceOptions($name, $modifier, bool $throwMissingFieldException = true)
    {
        if (!$throwMissingFieldException && !$this->has($name)) {
            return $this;
        }

        $child = $this->get($name);
        $type = $child->getConfig()->getOriginalType();
        $options = $child->getConfig()->getOriginalOptions();

        if (is_callable($modifier)) {
            $options = call_user_func($modifier, $options);
        }

        return $this->add($name, $type, $options);
    }
}
