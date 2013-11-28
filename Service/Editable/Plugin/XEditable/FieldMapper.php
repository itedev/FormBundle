<?php

namespace ITE\FormBundle\Service\Editable\Plugin\XEditable;

use ITE\FormBundle\Service\Editable\EditableManagerInterface;
use Symfony\Component\Form\Extension\Core\ChoiceList\ChoiceListInterface;
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
     */
    public function __construct(EditableManagerInterface $editableManager, Router $router)
    {
        $this->editableManager = $editableManager;
        $this->router = $router;
    }

    /**
     * @param $entity
     * @param $field
     * @param array $options
     * @return array
     */
    public function resolveParameters($entity, $field, $options = array())
    {
        $class = get_class($entity);

        $classMetadata = $this->editableManager->getClassMetadata($class);

        $form = $this->editableManager->createForm($entity, $field);
        $childForm = $form->get($field);
        $childView = $childForm->createView();

        $builtOptions = $this->buildOptions($childView, $childForm);

        $options = array_replace_recursive($builtOptions, $options, array(
            'pk' => $classMetadata->getIdentifierValues($entity),
            'params' => array(
                'class' => $class,
            ),
        ));

        return array(
            'value' => $options['value'],
            'element_data' => array(
                'extras' => (object) array(),
                'options' => $options,
            ),
        );
    }

    /**
     * @param FormView $view
     * @param FormInterface $form
     * @return string
     */
    public function buildOptions(FormView $view, FormInterface $form)
    {
        $config = $form->getConfig();
        $type = $config->getType();
        $formOptions = $config->getOptions();

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
                break;
            case 'email':
            case 'password':
            case 'url':
                $options['value'] = $view->vars['value'];
                break;
            case 'date':
                break;
            case 'datetime':
                break;
            case 'checklist':
                $options['source'] = array_map(function($choice) {
                    return array(
                        'value' => $choice->value,
                        'text' => $choice->text
                    );
                }, $view->vars['choices']);
                $options['value'] = $view->vars['value'];
                break;
            case 'select':
                $options['source'] = array_map(function($choice) {
                    return array(
                        'value' => $choice->value,
                        'text' => $choice->label
                    );
                }, $view->vars['choices']);
                $options['value'] = $view->vars['value'];
                break;
            case 'text':
                $options['value'] = $view->vars['value'];
                break;
        }

        return $options;
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
} 