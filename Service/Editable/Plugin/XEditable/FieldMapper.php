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
     * @var array $options
     */
    protected $options;

    /**
     * @var ContainerInterface $container
     */
    protected $container;

    /**
     * @param $options
     * @param ContainerInterface $container
     */
    public function __construct($options, ContainerInterface $container)
    {
        $this->options = $options;
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

        $editableManager = $this->container->get('ite_form.editable_manager');
        $classMetadata = $editableManager->getClassMetadata($class);

        $form = $editableManager->createForm($entity, $field);
        $childForm = $form->get($field);
        $childView = $childForm->createView();

        $elementData = $this->buildElementData($childView, $childForm, $text, array_replace_recursive(
            $this->options, $options
        ));
        $elementData['options'] = array_replace_recursive($elementData['options'], array(
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
     * @param array $options
     * @return array
     */
    public function buildElementData(FormView $view, FormInterface $form, &$text = null, $options = array())
    {
        $formOptions = $form->getConfig()->getOptions();

//        $xEditableResolvedType = isset($options['type']) ? $options['type'] : $this->guessType($view, $form);
        $xEditableResolvedType = $this->guessType($view, $form);

        $extras = array();
        $builtOptions = array(
            'type' => $xEditableResolvedType->getXEditableType(),
            'url' => $this->container->get('router')->generate('ite_form_plugin_x_editable_edit'),
            'title' => isset($formOptions['label'])
                    ? $formOptions['label']
                    : FormUtils::humanize($view->vars['name']),
            'name' => $view->vars['name'],
        );

        switch ($xEditableResolvedType->getXEditableType()) {
            case 'text':
            case 'textarea':
            case 'email':
            case 'password':
            case 'url':
                $builtOptions['value'] = $view->vars['value'];
                if (!isset($text)) {
                    $text = $view->vars['value'];
                }
                break;
            case 'combodate':
                switch ($xEditableResolvedType->getSfBaseType()) {
                    case 'datetime':
                        $withSeconds = $formOptions['with_seconds'];

                        $builtOptions = array_replace($builtOptions, $this->processDate(
                            $view->children['date'], $form->get('date')
                        ));

                        $builtOptions['format'] .= ',H,m' . ($withSeconds ? ',s' : '');
                        $builtOptions['template'] .= ' HH:mm' . ($withSeconds ? ':ss' : '');
                        $builtOptions['viewformat'] = 'YYYY-MM-DD HH:mm' . ($withSeconds ? ':ss' : '');

                        $builtOptions['value'] = implode('-', $view->vars['value']['date'])
                            . ' ' . implode(':', $view->vars['value']['time']);

                        if (!isset($text)) {
                            $text = $builtOptions['value'];
                        }
                        
                        $extras['view_transformer'] = 'datetime';
                        break;
                    case 'date':
                    case 'birthday':
                        $builtOptions = array_replace($builtOptions, $this->processDate($view, $form));
                        $builtOptions['viewformat'] = 'YYYY-MM-DD';

                        $builtOptions['value'] = implode('-', $view->vars['value']);
                        if (!isset($text)) {
                            $text = $builtOptions['value'];
                        }

                        $extras['view_transformer'] = 'date';
                        break;
                    case 'time':
                        $withSeconds = $formOptions['with_seconds'];

                        $builtOptions['format'] = 'H,m' . ($withSeconds ? ',s' : '');
                        $builtOptions['template'] = 'HH:mm' . ($withSeconds ? ':ss' : '');
                        $builtOptions['viewformat'] = 'HH:mm' . ($withSeconds ? ':ss' : '');

                        $builtOptions['value'] = implode(':', $view->vars['value']);
                        if (!isset($text)) {
                            $text = $builtOptions['value'];
                        }

                        $extras['view_transformer'] = 'time';
                        break;
                }
                break;
            case 'checklist':
                if ('checkbox' === $xEditableResolvedType->getSfBaseType()) {
                    $builtOptions['source'] = array(
                        1 => $options['title']
                    );
                    $builtOptions['value'] = !empty($view->vars['checked']) ? array(1) : array();

                    $extras['view_transformer'] = 'boolean';
                } else {
                    $builtOptions = array_replace($builtOptions, $this->processChoices($view, $text));
                }
                break;
            case 'select':
                $builtOptions = array_replace($builtOptions, $this->processChoices($view, $text));
                break;
            case 'select2':
                $builtOptions = array_replace($builtOptions, $this->processChoices($view, $text));
                $builtOptions['select2'] = array_replace_recursive(
                    $this->container->getParameter('ite_form.plugin.select2.options'),
                    (array) $view->vars['element_data']['options']
                );
                $extras['plugin'] = 'select2';
                break;
            case 'datetime':
                $builtOptions['datetimepicker'] = array_replace_recursive(
                    $this->container->getParameter('ite_form.plugin.bootstrap_datetimepicker.options'),
                    (array) $view->vars['element_data']['options']
                );
                $builtOptions['format'] = $builtOptions['datetimepicker']['format'];
                $builtOptions['viewformat'] = $builtOptions['datetimepicker']['format'];
                $builtOptions['value'] = $view->vars['value'];

                if (!isset($text)) {
                    $text = $view->vars['value'];
                }
                break;
        }

        return array(
            'extras' => $extras,
            'options' => array_replace_recursive($builtOptions, $options)
        );
    }

    /**
     * @param FormView $view
     * @param FormInterface $form
     * @return XEditableResolvedType
     */
    public function guessType(FormView $view, FormInterface $form)
    {
        $config = $form->getConfig();
        $resolvedFormType = $config->getType();
        $formOptions = $config->getOptions();
        $typeName = $resolvedFormType->getName();

        // firstly, try to guess plugin type
        if (preg_match('/^ite_select2_/', $typeName)) {
            // ite_select2_*
            return XEditableResolvedType::create('select2', $typeName, 'choice');
        } elseif (preg_match('/^ite_bootstrap_datetimepicker_/', $typeName)) {
            // ite_bootstrap_datetimepicker_*
            foreach (array('birthday', 'datetime', 'date', 'time') as $sfBaseType) {
                if (FormUtils::isResolvedFormTypeChildOf($resolvedFormType, $sfBaseType)) {
                    return XEditableResolvedType::create('datetime', $typeName, $sfBaseType);
                }
            }
        }

        // secondly, try to guess base types
        $type = null;
        foreach (array('textarea', 'email', 'password', 'url') as $sfBaseType) {
            if (FormUtils::isResolvedFormTypeChildOf($resolvedFormType, $sfBaseType)) {
                return XEditableResolvedType::create($sfBaseType, $typeName, $sfBaseType);
            }
        }
        foreach (array('birthday', 'datetime', 'date', 'time') as $sfBaseType) {
            if (FormUtils::isResolvedFormTypeChildOf($resolvedFormType, $sfBaseType)) {
                return XEditableResolvedType::create('combodate', $typeName, $sfBaseType);
            }
        }
        if (FormUtils::isResolvedFormTypeChildOf($resolvedFormType, 'choice')) {
            if ($formOptions['multiple']) {
                return XEditableResolvedType::create('checklist', $typeName, 'choice');
            } else {
                return XEditableResolvedType::create('select', $typeName, 'choice');
            }
        } elseif (FormUtils::isResolvedFormTypeChildOf($resolvedFormType, 'checkbox')) {
            return XEditableResolvedType::create('checklist', $typeName, 'checkbox');
        }

        return XEditableResolvedType::create('text', $typeName, 'text');
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

    /**
     * @param FormView $view
     * @param FormInterface $form
     * @return array
     */
    public function processDate(FormView $view, FormInterface $form)
    {
        $datePattern = $view->vars['date_pattern'];
        $pattern = $form->getConfig()->getAttribute('formatter')->getPattern();

        $dayCount = preg_match_all('/d/', $pattern, $matches);
        $monthCount = preg_match_all('/[M|L]/', $pattern, $matches);
        $yearCount = preg_match_all('/y/', $pattern, $matches);
        $yearCount = 1 !== $yearCount ? $yearCount : 4;

        $day = str_repeat('D', $dayCount);
        $month = str_repeat('M', $monthCount);
        $year = str_repeat('Y', $yearCount);

        $template = strtr($datePattern, array(
            '{{ year }}' => $year . ' ',
            '{{ month }}' => $month . ' ',
            '{{ day }}' => $day . ' ',
        ));

        return array(
            'format' => 'YYYY,M,D',
            'template' => $template,
        );
    }
} 