<?php

namespace ITE\FormBundle\Validation\Constraints;

use ITE\FormBundle\Validation\Constraint;
use Symfony\Component\Intl\Intl;

/**
 * Class Currency
 *
 * @Annotation
 * @Target({"PROPERTY"})
 *
 * @see Symfony\Component\Validator\Constraints\Currency
 * @see Symfony\Component\Validator\Constraints\CurrencyValidator
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class Currency extends Constraint
{
    public $message = 'This value is not a valid currency.';

    /**
     * {@inheritdoc}
     */
    public function __construct($options = null)
    {
        parent::__construct($options);

        $currencies = Intl::getCurrencyBundle()->getCurrencyNames();
        $this->setAttribute('currencies', $currencies);
    }

}