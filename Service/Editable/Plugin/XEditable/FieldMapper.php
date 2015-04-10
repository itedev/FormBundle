<?php

namespace ITE\FormBundle\Service\Editable\Plugin\XEditable;

use ITE\FormBundle\Service\Editable\EditableManagerInterface;
use ITE\FormBundle\SF\Plugin\BootstrapDatetimepickerPlugin;
use ITE\FormBundle\SF\Plugin\Select2Plugin;
use ITE\FormBundle\SF\Plugin\TinymcePlugin;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\Extension\Core\ChoiceList\ChoiceListInterface;
use Symfony\Component\Form\Extension\Core\View\ChoiceView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Routing\Router;
use ITE\FormBundle\Util\FormUtils;

/**
 * Class FieldMapper
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
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
        $em = $this->container->get('doctrine.orm.entity_manager');
        $classMetadata = $em->getClassMetadata($class);

        $form = $editableManager->createForm($entity, $field);
        $childForm = $form->get($field);
        $childView = $childForm->createView();

        $elementData = $this->buildElementData($childView, $childForm, $text, array_replace_recursive(
            $this->options, $options
        ));

        $elementData['options'] = array_replace_recursive($elementData['options'], array(
            'pk' => $classMetadata->getIdentifierValues($entity),
            'params' => array(
                'class' => $this->container->get('ite_form.param_protector')->encrypt($class),
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
                        $withMinutes = $formOptions['with_minutes'];
                        $withSeconds = $formOptions['with_seconds'];

                        $builtOptions = array_replace($builtOptions, $this->processDate(
                            $view->children['date'], $form->get('date')
                        ));

                        $format = array('H');
                        $template = array('HH');
                        $viewFormat = array('HH');
                        $value = array(str_pad($view->vars['value']['time']['hour'], 2, '0', STR_PAD_LEFT));

                        if ($withMinutes) {
                            $format[] = 'm';
                            $template[] = 'mm';
                            $viewFormat[] = 'mm';
                            $value[] = str_pad($view->vars['value']['time']['minute'], 2, '0', STR_PAD_LEFT);
                        }
                        if ($withSeconds) {
                            $format[] = 's';
                            $template[] = 'ss';
                            $viewFormat[] = 'ss';
                            $value[] = str_pad($view->vars['value']['time']['second'], 2, '0', STR_PAD_LEFT);
                        }

                        $builtOptions['format'] .= ',' . implode(',', $format);
                        $builtOptions['template'] .= ' ' . implode(':', $template);
                        $builtOptions['viewformat'] = 'YYYY-MM-DD ' . implode(':', $viewFormat);
                        $builtOptions['value'] = sprintf(
                            '%04d-%02d-%02d',
                            $view->vars['value']['date']['year'],
                            $view->vars['value']['date']['month'],
                            $view->vars['value']['date']['day']
                        ) . ' ' . implode(':', $value);

                        if (!isset($text)) {
                            $text = $builtOptions['value'];
                        }
                        
                        $extras['view_transformer'] = 'datetime';
                        break;
                    case 'date':
                    case 'birthday':
                        $builtOptions = array_replace($builtOptions, $this->processDate($view, $form));
                        $builtOptions['viewformat'] = 'YYYY-MM-DD';
                        $builtOptions['value'] = sprintf(
                            '%04d-%02d-%02d',
                            $view->vars['value']['year'],
                            $view->vars['value']['month'],
                            $view->vars['value']['day']
                        );
                        if (!isset($text)) {
                            $text = $builtOptions['value'];
                        }

                        $extras['view_transformer'] = 'date';
                        break;
                    case 'time':
                        $withMinutes = $formOptions['with_minutes'];
                        $withSeconds = $formOptions['with_seconds'];

                        $format = array('H');
                        $template = array('HH');
                        $viewFormat = array('HH');
                        $value = array(str_pad($view->vars['value']['hour'], 2, '0', STR_PAD_LEFT));

                        if ($withMinutes) {
                            $format[] = 'm';
                            $template[] = 'mm';
                            $viewFormat[] = 'mm';
                            $value[] = str_pad($view->vars['value']['minute'], 2, '0', STR_PAD_LEFT);
                        }
                        if ($withSeconds) {
                            $format[] = 's';
                            $template[] = 'ss';
                            $viewFormat[] = 'ss';
                            $value[] = str_pad($view->vars['value']['second'], 2, '0', STR_PAD_LEFT);
                        }

                        $builtOptions['format'] = implode(',', $format);
                        $builtOptions['template'] = implode(':', $template);
                        $builtOptions['viewformat'] = implode(':', $viewFormat);
                        $builtOptions['value'] = implode(':', $value);

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
                        1 => $builtOptions['title']
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
                    (array) $view->vars['plugins'][Select2Plugin::getName()]['options']
                );
                $extras['plugin'] = 'select2';
                break;
            case 'datetime':
                $builtOptions['datetimepicker'] = array_replace_recursive(
                    $this->container->getParameter('ite_form.plugin.bootstrap_datetimepicker.options'),
                    (array) $view->vars['plugins'][BootstrapDatetimepickerPlugin::getName()]['options']
                );
                $builtOptions['format'] = $builtOptions['datetimepicker']['format'];
                $builtOptions['viewformat'] = $builtOptions['datetimepicker']['format'];
                $builtOptions['value'] = $view->vars['value'];

                if (!isset($text)) {
                    $text = $view->vars['value'];
                }
                break;
            case 'tinymce':
                $builtOptions['tinymce'] = array_replace_recursive(
                    $this->container->getParameter('ite_form.plugin.tinymce.options'),
                    (array) $view->vars['plugins'][TinymcePlugin::getName()]['options']
                );
                $builtOptions['onblur'] = 'ignore';
                $extras['plugin'] = 'tinymce';

                if (!isset($text)) {
                    $text = $view->vars['value'];
                }
                break;
            case 'knob':
                $builtOptions['knob'] = array_replace_recursive(
                    $this->container->getParameter('ite_form.plugin.knob.options'),
                    (array) $view->vars['element_data']['options']
                );
                $extras['plugin'] = 'knob';

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
        } elseif ('ite_tinymce_textarea' === $typeName) {
            return XEditableResolvedType::create('tinymce', $typeName, 'textarea');
        } elseif ('ite_knob_number' === $typeName) {
            return XEditableResolvedType::create('knob', $typeName, 'number');
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
        $value = array();
        foreach ($view->vars['choices'] as $index => $choice) {
            /** @var $choice ChoiceView */
            $source[] = array(
                'value' => $choice->value,
                'text' => $choice->label
            );

            if ($isTextSet) {
                continue;
            }
            if (is_array($view->vars['value'])) {
                if ($view->vars['expanded']) {
                    if ($view->vars['value'][$index]) {
                        $text[] = $choice->label;
                        $value[] = $choice->value;
                    }
                } else {
                    if (false !== array_search($choice->value, $view->vars['value'], true)) {
                        $text[] = $choice->label;
                        $value[] = $choice->value;
                    }
                }
            } else {
                if ($choice->value === $view->vars['value']) {
                    $text[] = $choice->label;
                    $value[] = $view->vars['value'];
                }
            }
        }

        if (!$isTextSet) {
            $text = implode('<br />', $text);
        }
        $value = implode(',', $value);

        return array(
            'source' => $source,
            'value' => $value,
        );
    }

    /**
     * @param FormView $view
     * @return array
     */
    protected function getDateTimeValue(FormView $view)
    {
        $value = array();
        if (isset($view->children['date'])) {
            // datetime
            $value['date'] = array();
            foreach (array('year', 'month', 'day') as $field) {
                $choiceValue = $view->vars['value']['date'][$field];
                $choices = $view->children['date']->children[$field]->vars['choices'];
                $selectedChoice = current(array_filter($choices, function(ChoiceView $choice) use ($choiceValue) {
                    return $choice->value === $choiceValue;
                }));
                $value['date'][$field] = $selectedChoice->label;
            }
            $value['time'] = array();
            foreach (array('hour', 'minute', 'second') as $field) {
                if (!isset($view->vars['value']['time'][$field])) {
                    continue;
                }
                $choiceValue = $view->vars['value']['time'][$field];
                $choices = $view->children['time']->children[$field]->vars['choices'];
                $selectedChoice = current(array_filter($choices, function(ChoiceView $choice) use ($choiceValue) {
                    return $choice->value === $choiceValue;
                }));
                $value['time'][$field] = $selectedChoice->label;
            }
        } elseif (isset($view->children['year'])) {
            // date
            foreach (array('year', 'month', 'day') as $field) {
                $choiceValue = $view->vars['value'][$field];
                $choices = $view->children[$field]->vars['choices'];
                $selectedChoice = current(array_filter($choices, function(ChoiceView $choice) use ($choiceValue) {
                    return $choice->value === $choiceValue;
                }));
                $value[$field] = $selectedChoice->label;
            }
        } elseif (isset($view->children['hour'])) {
            // time
            foreach (array('hour', 'minute', 'second') as $field) {
                if (!isset($view->vars['value'][$field])) {
                    continue;
                }
                $choiceValue = $view->vars['value'][$field];
                $choices = $view->children[$field]->vars['choices'];
                $selectedChoice = current(array_filter($choices, function(ChoiceView $choice) use ($choiceValue) {
                    return $choice->value === $choiceValue;
                }));
                $value[$field] = $selectedChoice->label;
            }
        }

        return $value;
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