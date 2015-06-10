<?php

namespace ITE\FormBundle\Validation\Constraints;

use ITE\FormBundle\Validation\ClientConstraint;
use Symfony\Component\Validator\Exception\ConstraintDefinitionException;

/**
 * Class AbstractComparison
 *
 * @see Symfony\Component\Validator\Constraints\AbstractComparison
 * @see Symfony\Component\Validator\Constraints\AbstractComparisonValidator
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
abstract class AbstractComparison extends ClientConstraint
{
    public $message;
    public $value;

    /**
     * {@inheritdoc}
     */
    public function __construct($options = null)
    {
        if (is_array($options) && !isset($options['value'])) {
            throw new ConstraintDefinitionException(sprintf(
                'The %s constraint requires the "value" option to be set.',
                get_class($this)
            ));
        }

        parent::__construct($options);
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultOption()
    {
        return 'value';
    }
}