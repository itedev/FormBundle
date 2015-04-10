<?php

namespace ITE\FormBundle\Service\Validation;

use ITE\FormBundle\Util\FormAccessor;
use ITE\FormBundle\Util\FormUtils;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

/**
 * Class FormConstraint
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class FormConstraint
{
    /**
     * @var FormInterface $root
     */
    protected $form;

    /**
     * @var ConstraintMetadataInterface $constraintMetadata
     */
    protected $constraintMetadata;

    /**
     * @var FormView $view
     */
    protected $view;

    /**
     * @param FormInterface $form
     * @param ConstraintMetadataInterface $constraintMetadata
     */
    public function __construct(FormInterface $form, ConstraintMetadataInterface $constraintMetadata)
    {
        $this->form = $form;
        $this->constraintMetadata = $constraintMetadata;
    }

    /**
     * Get constraintMetadata
     *
     * @return ConstraintMetadataInterface
     */
    public function getConstraintMetadata()
    {
        return $this->constraintMetadata;
    }

    /**
     * Get form
     *
     * @return FormInterface
     */
    public function getForm()
    {
        return $this->form;
    }

    /**
     * @param FormView $rootView
     * @return null|FormView
     */
    public function initializeView(FormView $rootView)
    {
        $propertyPath = FormUtils::getFullName($this->form);

        $this->view = FormUtils::getViewByFullName($rootView, $propertyPath);

        return $this->view;
    }

    /**
     * Get view
     *
     * @return FormView
     */
    public function getView()
    {
        return $this->view;
    }

} 