<?php

namespace ITE\FormBundle\Validation\ConstraintConverter;

use ITE\FormBundle\Validation\AbstractConstraintConverter;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\Choice;
use ITE\FormBundle\Validation\Constraints\Choice as ClientChoice;
use Symfony\Component\Validator\Exception\ConstraintDefinitionException;

/**
 * Class ChoiceConverter
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class ChoiceConverter extends AbstractConstraintConverter
{
    /**
     * {@inheritdoc}
     */
    public function supports(Constraint $constraint)
    {
        return $constraint instanceof Choice;
    }

    /**
     * {@inheritdoc}
     */
    public function convert(Constraint $constraint)
    {
        /** @var Choice $constraint */
        $options = $this->getOptions($constraint, 'ITE\\FormBundle\\Validation\\Constraints\\Choice');

        return new ClientChoice($options);
    }

    /**
     * @param Choice $constraint
     * @return array
     */
    protected function getChoices(Choice $constraint)
    {
        if ($constraint->callback) {
            if (!is_callable($choices = array($this->context->getClassName(), $constraint->callback))
                && !is_callable($choices = $constraint->callback)
            ) {
                throw new ConstraintDefinitionException('The Choice constraint expects a valid callback');
            }
            $choices = call_user_func($choices);
        } else {
            $choices = $constraint->choices;
        }

        return $choices;
    }
}