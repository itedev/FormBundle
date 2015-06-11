<?php

namespace ITE\FormBundle\SF\Form;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\ResolvedFormTypeInterface;

/**
 * Class ClientFormViewBuilder
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class ClientFormViewBuilder implements ClientFormViewBuilderInterface
{
    /**
     * {@inheritdoc}
     */
    public function createClientView(FormView $view, FormInterface $form, ClientFormView $parent = null)
    {
        /** @var FormInterface $formParent */
        $parentForm = $form->getParent();
        if (null === $parent && $parentForm) {
            $parentView = $view->parent;
            $parent = $this->createClientView($parentView, $parentForm);
        }

        /** @var ResolvedFormTypeInterface $type */
        $type = $form->getConfig()->getType();
        $options = $form->getConfig()->getOptions();

        $clientView = $this->newClientView($view, $form, $parent);

        $this->buildClientView($type, $clientView, $view, $form, $options);

        $children = $form->all();
        foreach ($children as $name => $childForm) {
            /** @var FormInterface $childForm */
            $childView = $view[$name];

            $clientView->addChild($name, $this->createClientView($childView, $childForm, $clientView));
        }

        return $clientView;
    }

    /**
     * @param FormView $view
     * @param FormInterface $form
     * @param ClientFormView $parent
     * @return ClientFormView
     */
    protected function newClientView(FormView $view, FormInterface $form, ClientFormView $parent = null)
    {
        $clientView = new ClientFormView($parent);
        $clientView->setOptions([
            'id' => isset($view->vars['attr']['id']) ? $view->vars['attr']['id'] : $view->vars['id'],
            'name' => $view->vars['name'],
            'full_name' => $view->vars['full_name'],
        ]);

        return $clientView;
    }

    /**
     * @param ResolvedFormTypeInterface $type
     * @param ClientFormView $clientView
     * @param FormView $view
     * @param FormInterface $form
     * @param array $options
     */
    protected function buildClientView(ResolvedFormTypeInterface $type, ClientFormView $clientView, FormView $view,
        FormInterface $form, array $options)
    {
        /** @var ResolvedFormTypeInterface $parent */
        $parentType = $type->getParent();
        if (null !== $parentType) {
            $this->buildClientView($parentType, $clientView, $view, $form, $options);
        }

        $innerType = $type->getInnerType();
        if ($innerType instanceof ClientFormTypeInterface) {
            $innerType->buildClientView($clientView, $view, $form, $options);
        }

        foreach ($type->getTypeExtensions() as $extension) {
            if ($extension instanceof ClientFormTypeExtensionInterface) {
                $extension->buildClientView($clientView, $view, $form, $options);
            }
        }
    }

}