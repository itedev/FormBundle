<?php

namespace ITE\FormBundle\Form;

use ITE\FormBundle\Form\Builder\FormBuilder;
use ITE\FormBundle\Form\EventListener\Component\Hierarchical\HierarchicalAddChildSubscriber;
use ITE\FormBundle\Form\EventListener\Component\Hierarchical\HierarchicalSetDataSubscriber;
use ITE\FormBundle\Util\FormUtils;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Form\Form as BaseForm;

/**
 * Class Form
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class Form extends BaseForm implements FormInterface
{
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
    public function replaceType($name, $type)
    {
        $child = $this->get($name);
        $options = $child->getConfig()->getOptions();

        return $this->add($name, $type, $options);
    }

    /**
     * {@inheritdoc}
     */
    public function replaceOptions($name, array $options)
    {
        $child = $this->get($name);
        $currentOptions = $child->getConfig()->getOptions();
        $type = $child->getConfig()->getType()->getName();

        $options = array_replace_recursive($currentOptions, $options);

        return $this->add($name, $type, $options);
    }
}
