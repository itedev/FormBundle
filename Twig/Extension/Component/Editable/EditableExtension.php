<?php

namespace ITE\FormBundle\Twig\Extension\Component\Editable;

use ITE\FormatterBundle\Twig\PropertyPathAwareFilter;
use ITE\FormBundle\Component\Editable\EditableManagerInterface;
use ITE\FormBundle\Twig\NodeVisitor\EditableNodeVisitor;

/**
 * Class EditableExtension
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class EditableExtension extends \Twig_Extension
{
    /**
     * @var EditableManagerInterface
     */
    protected $editableManager;

    /**
     * EditableExtension constructor.
     *
     * @param EditableManagerInterface $editableManager
     */
    public function __construct(EditableManagerInterface $editableManager)
    {
        $this->editableManager = $editableManager;
    }

    /**
     * {@inheritdoc}
     */
    public function getNodeVisitors()
    {
        return [
            new EditableNodeVisitor(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return [
            new PropertyPathAwareFilter('ite_editable', [$this, 'editable'], ['is_safe' => ['html']]),
        ];
    }

    /**
     * @param array|null $propertyPath
     * @param mixed $value
     * @param array $options
     * @return string
     */
    public function editable($propertyPath, $value, array $options = [])
    {
        if (null === $propertyPath) {
            return '';
        }

        $entity = $propertyPath['object'];
        $property = $propertyPath['property'];

        return $this->editableManager->getWidget($entity, $property, $options);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'ite_form.twig.extension.editable';
    }
}
