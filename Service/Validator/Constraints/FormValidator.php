<?php

namespace ITE\FormBundle\Service\Validator\Constraints;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * @author Bernhard Schussek <bschussek@gmail.com>
 */
class FormValidator extends ConstraintValidator
{
    /**
     * {@inheritdoc}
     */
    public function validate($form, Constraint $constraint)
    {
        if (!$form instanceof FormInterface) {
            return;
        }

        /* @var FormInterface $form */
        $config = $form->getConfig();

        if ($form->isSynchronized()) {
            // Validate the form data only if transformation succeeded
            $groups = self::getValidationGroups($form);

            // Validate the data against its own constraints
            if (self::allowDataWalking($form)) {
                foreach ($groups as $group) {
                    $this->context->validate($form->getData(), 'data', $group, true);
                }
            }

            // Validate the data against the constraints defined
            // in the form
            $constraints = $config->getOption('constraints');
            foreach ($constraints as $constraint) {
                foreach ($groups as $group) {
                    if (in_array($group, $constraint->groups)) {
                        $this->context->validateValue($form->getData(), $constraint, 'data', $group);

                        // Prevent duplicate validation
                        continue 2;
                    }
                }
            }
        }
    }

    /**
     * Returns whether the data of a form may be walked.
     *
     * @param  FormInterface $form The form to test.
     *
     * @return Boolean Whether the graph walker may walk the data.
     */
    private static function allowDataWalking(FormInterface $form)
    {
        $data = $form->getData();

        // Scalar values cannot have mapped constraints
        if (!is_object($data) && !is_array($data)) {
            return false;
        }

        // Root forms are always validated
        if ($form->isRoot()) {
            return true;
        }

        // Non-root forms are validated if validation cascading
        // is enabled in all ancestor forms
        while (null !== ($form = $form->getParent())) {
            if (!$form->getConfig()->getOption('cascade_validation')) {
                return false;
            }
        }

        return true;
    }

    /**
     * Returns the validation groups of the given form.
     *
     * @param  FormInterface $form The form.
     *
     * @return array The validation groups.
     */
    private static function getValidationGroups(FormInterface $form)
    {
        do {
            $groups = $form->getConfig()->getOption('validation_groups');

            if (null !== $groups) {
                return self::resolveValidationGroups($groups, $form);
            }

            $form = $form->getParent();
        } while (null !== $form);

        return array(Constraint::DEFAULT_GROUP);
    }

    /**
     * Post-processes the validation groups option for a given form.
     *
     * @param array|callable $groups The validation groups.
     * @param FormInterface  $form   The validated form.
     *
     * @return array The validation groups.
     */
    private static function resolveValidationGroups($groups, FormInterface $form)
    {
        if (!is_string($groups) && is_callable($groups)) {
            $groups = call_user_func($groups, $form);
        }

        return (array) $groups;
    }
}
