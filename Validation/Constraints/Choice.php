<?php

namespace ITE\FormBundle\Validation\Constraints;

use ITE\FormBundle\Validation\Constraint;

/**
 * Class Choice
 *
 * @Annotation
 * @Target({"PROPERTY"})
 *
 * @see Symfony\Component\Validator\Constraints\Choice
 * @see Symfony\Component\Validator\Constraints\ChoiceValidator
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class Choice extends Constraint
{
    public $choices;
    public $callback;
    public $multiple = false;
    public $strict = false;
    public $min;
    public $max;
    public $message = 'The value you selected is not a valid choice.';
    public $multipleMessage = 'One or more of the given values is invalid.';
    public $minMessage = 'You must select at least {{ limit }} choice.|You must select at least {{ limit }} choices.';
    public $maxMessage = 'You must select at most {{ limit }} choice.|You must select at most {{ limit }} choices.';

    /**
     * {@inheritdoc}
     */
    public function getDefaultOption()
    {
        return 'choices';
    }
}