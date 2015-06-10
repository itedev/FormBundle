<?php

namespace ITE\FormBundle\Validation\Constraints;

use ITE\FormBundle\Validation\ClientConstraint;
use Symfony\Component\Intl\Intl;

/**
 * Class Language
 *
 * @Annotation
 * @Target({"PROPERTY"})
 *
 * @see Symfony\Component\Validator\Constraints\Language
 * @see Symfony\Component\Validator\Constraints\LanguageValidator
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class Language extends ClientConstraint
{
    public $message = 'This value is not a valid language.';

    /**
     * {@inheritdoc}
     */
    public function __construct($options = null)
    {
        parent::__construct($options);

        $languages = Intl::getLanguageBundle()->getLanguageNames();
        $this->setAttribute('languages', $languages);
    }

}