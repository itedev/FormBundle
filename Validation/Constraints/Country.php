<?php

namespace ITE\FormBundle\Validation\Constraints;

use ITE\FormBundle\Validation\ClientConstraint;
use Symfony\Component\Intl\Intl;

/**
 * Class Country
 *
 * @Annotation
 * @Target({"PROPERTY"})
 *
 * @see Symfony\Component\Validator\Constraints\Country
 * @see Symfony\Component\Validator\Constraints\CountryValidator
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class Country extends ClientConstraint
{
    public $message = 'This value is not a valid country.';

    /**
     * {@inheritdoc}
     */
    public function __construct($options = null)
    {
        parent::__construct($options);

        $countries = Intl::getRegionBundle()->getCountryNames();
        $this->setAttribute('countries', $countries);
    }
}
