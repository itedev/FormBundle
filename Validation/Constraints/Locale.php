<?php

namespace ITE\FormBundle\Validation\Constraints;

use ITE\FormBundle\Validation\ClientConstraint;
use Symfony\Component\Intl\Intl;

/**
 * Class Locale
 *
 * @Annotation
 * @Target({"PROPERTY"})
 *
 * @see Symfony\Component\Validator\Constraints\Locale
 * @see Symfony\Component\Validator\Constraints\LocaleValidator
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class Locale extends ClientConstraint
{
    public $message = 'This value is not a valid locale.';

    /**
     * {@inheritdoc}
     */
    public function __construct($options = null)
    {
        parent::__construct($options);

        $locales = Intl::getLocaleBundle()->getLocaleNames();
        $this->setAttribute('locales', $locales);
    }

}