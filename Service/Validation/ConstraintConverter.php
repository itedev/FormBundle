<?php

namespace ITE\FormBundle\Service\Validation;

use Doctrine\Common\Inflector\Inflector;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Validator\Constraint;

/**
 * Class ConstraintConverter
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class ConstraintConverter implements ConstraintConverterInterface
{
    /**
     * @var Constraint $constraint
     */
    protected $constraint;

    /**
     * @var TranslatorInterface $translator
     */
    protected $translator;

    /**
     * @var string|null $translationDomain
     */
    protected $translationDomain;

    /**
     * @param Constraint $constraint
     * @param TranslatorInterface $translator
     * @param null $translationDomain
     */
    public function __construct(Constraint $constraint, TranslatorInterface $translator, $translationDomain = null)
    {
        $this->constraint = $constraint;
        $this->translator = $translator;
        $this->translationDomain = $translationDomain;
    }

    /**
     * @return string
     */
    public function getType()
    {
        $constraintClass = get_class($this->constraint);
        $constraintClassName = substr($constraintClass, strrpos($constraintClass, '\\') + 1);

        return Inflector::camelize($constraintClassName);
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->translate($this->constraint->message);
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return array();
    }

    /**
     * @param $message
     * @param array $parameters
     * @param null $pluralization
     * @return string
     */
    protected function translate($message, $parameters = array(), $pluralization = null)
    {
        if (null === $pluralization) {
            $translatedMessage = $this->translator->trans($message, $parameters, $this->translationDomain);
        } else {
            try {
                $translatedMessage = $this->translator->transChoice($message, $pluralization, $parameters, $this->translationDomain);
            } catch (\InvalidArgumentException $e) {
                $translatedMessage = $this->translator->trans($message, $parameters, $this->translationDomain);
            }
        }

        return $translatedMessage;
    }
} 