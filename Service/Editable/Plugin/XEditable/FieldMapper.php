<?php

namespace ITE\FormBundle\Service\Editable\Plugin\XEditable;

use ITE\FormBundle\Service\Editable\EditableManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\Extension\Core\ChoiceList\ChoiceListInterface;
use Symfony\Component\Form\Extension\Core\View\ChoiceView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Routing\Router;
use ITE\FormBundle\Util\FormUtils;

/**
 * Class FieldMapper
 * @package ITE\FormBundle\Service\Editable\Plugin\XEditable
 */
class FieldMapper
{
    /**
     * @var EditableManagerInterface $editableManager
     */
    protected $editableManager;

    /**
     * @var Router $router
     */
    protected $router;

    /**
     * @var ContainerInterface $container
     */
    protected $container;

    /**
     * @var array $guessOrder
     */
    protected $guessOrder = array(
        'textarea',
        'email',
        'password',
        'url',
        'date',
        'datetime',
    );

    /**
     * @param EditableManagerInterface $editableManager
     * @param Router $router
     * @param ContainerInterface $container
     */
    public function __construct(EditableManagerInterface $editableManager, Router $router, ContainerInterface $container)
    {
        $this->editableManager = $editableManager;
        $this->router = $router;
        $this->container = $container;
    }

    /**
     * @param $entity
     * @param $field
     * @param null $text
     * @param array $options
     * @return array
     */
    public function resolveParameters($entity, $field, &$text = null, $options = array())
    {
        $class = get_class($entity);

        $classMetadata = $this->editableManager->getClassMetadata($class);

        $form = $this->editableManager->createForm($entity, $field);
        $childForm = $form->get($field);
        $childView = $childForm->createView();

        $elementData = $this->buildElementData($childView, $childForm, $text);
        $elementData['options'] = array_replace_recursive($elementData['options'], $options, array(
            'pk' => $classMetadata->getIdentifierValues($entity),
            'params' => array(
                'class' => $class,
            ),
        ));

        return array(
            'text' => $text,
            'element_data' => array(
                'extras' => (object) $elementData['extras'],
                'options' => $elementData['options']
            )
        );
    }

    /**
     * @param FormView $view
     * @param FormInterface $form
     * @param null $text
     * @return array
     */
    public function buildElementData(FormView $view, FormInterface $form, &$text = null)
    {
        $config = $form->getConfig();
        $resolvedFormType = $config->getType();
        $formOptions = $config->getOptions();

        $extras = array();
        $options = array(
            'type' => $this->guessType($view, $form),
            'url' => $this->router->generate('ite_form_plugin_x_editable_edit'),
            'title' => isset($formOptions['label'])
                    ? $formOptions['label']
                    : FormUtils::humanize($view->vars['name']),
            'name' => $view->vars['name'],
        );

        switch ($options['type']) {
            case 'textarea':
                $options['value'] = $view->vars['value'];
                if (!isset($text)) {
                    $text = $view->vars['value'];
                }
                break;
            case 'email':
            case 'password':
            case 'url':
                $options['value'] = $view->vars['value'];
                if (!isset($text)) {
                    $text = $view->vars['value'];
                }
                break;
            case 'date':
                break;
            case 'datetime':
                $options['value'] = $view->vars['value'];
                if (!isset($text)) {
                    $text = $view->vars['value'];
                }
                break;
            case 'checklist':
                if (FormUtils::isResolvedFormTypeChildOf($resolvedFormType, 'checkbox')) {
                    $options['source'] = array(
                        1 => $options['title']
                    );
                    $options['value'] = !empty($view->vars['checked']) ? array(1) : array();
                    $extras['boolean'] = true;
                } else {
                    $options = array_replace($options, $this->processChoices($view, $text));
                }
                break;
            case 'select':
                $options = array_replace($options, $this->processChoices($view, $text));
                break;
            case 'select2':
                $options = array_replace($options, $this->processChoices($view, $text));
                $options['select2'] = array_replace_recursive(
                    $this->container->getParameter('ite_form.plugin.select2.options'),
                    $formOptions['plugin_options']
                );
                $extras['plugin'] = 'select2';
                break;
            case 'text':
                $options['value'] = $view->vars['value'];
                if (!isset($text)) {
                    $text = $view->vars['value'];
                }
                break;
        }

        return array(
            'extras' => $extras,
            'options' => $options,
        );
    }

    /**
     * @param FormView $view
     * @param FormInterface $form
     * @return string
     */
    public function guessType(FormView $view, FormInterface $form)
    {
        $config = $form->getConfig();
        $resolvedFormType = $config->getType();
        $formOptions = $config->getOptions();

        $typeName = $resolvedFormType->getName();
        if (preg_match('/^ite_select2_/', $typeName)) {
            return 'select2';
        }

        $type = null;
        foreach ($this->guessOrder as $fieldType) {
            if (FormUtils::isResolvedFormTypeChildOf($resolvedFormType, $fieldType)) {
                return $fieldType;
            }
        }
        if (FormUtils::isResolvedFormTypeChildOf($resolvedFormType, 'choice')) {
            if ($formOptions['multiple']) {
                return 'checklist';
            } else {
                return 'select';
            }
        }
        if (FormUtils::isResolvedFormTypeChildOf($resolvedFormType, 'checkbox')) {
            return 'checklist';
        }

        return 'text';
    }

    /**
     * @param FormView $view
     * @param null $text
     * @return array
     */
    protected function processChoices(FormView $view, &$text = null)
    {
        $isTextSet = isset($text);
        if (!$isTextSet) {
            $text = array();
        }

        $source = array();
        foreach ($view->vars['choices'] as $choice) {
            /** @var $choice ChoiceView */
            $source[] = array(
                'value' => $choice->value,
                'text' => $choice->label
            );

            if ($isTextSet) {
                continue;
            }
            if (is_array($view->vars['value'])) {
                if (false !== array_search($choice->value, $view->vars['value'], true)) {
                    $text[] = $choice->label;
                }
            } else {
                if ($choice->value === $view->vars['value']) {
                    $text[] = $choice->label;
                }
            }
        }

        if (!$isTextSet) {
            $text = implode('<br />', $text);
        }

        $value = is_array($view->vars['value'])
            ? implode(',', $view->vars['value'])
            : $view->vars['value'];

        return array(
            'source' => $source,
            'value' => $value,
        );
    }
} 