<?php

namespace ITE\FormBundle\Service\Validation;

use Doctrine\Common\Inflector\Inflector;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\AbstractComparison;
use Symfony\Component\Validator\Constraints\CardScheme;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\Count;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Ip;
use Symfony\Component\Validator\Constraints\Issn;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Range;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Type;
use Symfony\Component\Validator\Constraints\Url;

/**
 * Class ConstraintMetadataFactory
 * @package ITE\FormBundle\Service\Validation
 */
class ConstraintMetadataFactory
{
    /**
     * @var array $validConstraints
     */
    protected $validConstraints = array(
        // simple
        'NotBlank',
        'Blank',
        'NotNull',
        'Null',
        'True',
        'False',
        'Type',
        'Email',
        'Url',
        'Regex',
        'Ip',
        'EqualTo',
        'NotEqualTo',
        'IdenticalTo',
        'NotIdenticalTo',
        'LessThan',
        'LessThanOrEqual',
        'GreaterThan',
        'GreaterThanOrEqual',
        'Date',
        'DateTime',
        'Time',
//        'UniqueEntity',
        'Locale',
        'Country',
        'CardScheme',
        'Currency',
        'Luhn',
        'Iban',
        'Issn',
//        'UserPassword',
        // complex
        'Length',
        'Range',
        'Choice',
//        'Collection',
        'Count',
//        'Isbn',
//        'Callback',
//        'All',
//        'Valid',
    );

    /**
     * @var array $simpleConstraints
     */
    protected $simpleConstraints = array(
        'NotBlank',
        'Blank',
        'NotNull',
        'Null',
        'True',
        'False',
        'Type',
        'Email',
        'Url',
        'Regex',
        'Ip',
        'EqualTo',
        'NotEqualTo',
        'IdenticalTo',
        'NotIdenticalTo',
        'LessThan',
        'LessThanOrEqual',
        'GreaterThan',
        'GreaterThanOrEqual',
        'Date',
        'DateTime',
        'Time',
//        'UniqueEntity',
        'Locale',
        'Country',
        'CardScheme',
        'Currency',
        'Luhn',
        'Iban',
        'Issn',
//        'UserPassword'
    );

    /**
     * @var array $comparizonConstraints
     */
    protected $comparizonConstraints = array(
        'EqualTo',
        'NotEqualTo',
        'IdenticalTo',
        'NotIdenticalTo',
        'LessThan',
        'LessThanOrEqual',
        'GreaterThan',
        'GreaterThanOrEqual',
    );

    /**
     * @var TranslatorInterface $translator
     */
    protected $translator;

    /**
     * @var string|null $translationDomain
     */
    protected $translationDomain;

    /**
     * @var array $constraintMetadataCache
     */
    protected $constraintMetadataCache = array();

    /**
     * @param TranslatorInterface $translator
     * @param null $translationDomain
     */
    public function __construct(TranslatorInterface $translator, $translationDomain = null)
    {
        $this->translator = $translator;
        $this->translationDomain = $translationDomain;
    }

    /**
     * @param Constraint $constraint
     * @return null
     */
    public function getMetadataFor(Constraint $constraint)
    {
        $hash = md5(serialize($constraint));
        if (isset($this->constraintMetadataCache[$hash])) {
            return $this->constraintMetadataCache[$hash];
        }

        $constraintClass = get_class($constraint);
        if (0 !== strpos($constraintClass, 'Symfony\Component\Validator\Constraints')) {
            return null;
        }

        $constraintClassName = substr($constraintClass, strrpos($constraintClass, '\\') + 1);
        if (!in_array($constraintClassName, $this->validConstraints)) {
            return null;
        }

        $method = sprintf('process%sConstraint', $constraintClassName);
        if (method_exists($this, $method)) {
            // specific process method
            $constraintProperties = $this->$method($constraint);
        } elseif (in_array($constraintClassName, $this->comparizonConstraints)) {
            // comparizon process method
            $constraintProperties = $this->processAbstractComparisonConstraint($constraint);
        } else {
            // common process method
            $constraintProperties = $this->processConstraint($constraint);
        }
        if (in_array($constraintClassName, $this->simpleConstraints)) {
            $constraintProperties['type'] = Inflector::camelize($constraintClassName);
        }

        $this->constraintMetadataCache[$hash] = new ConstraintMetadata(
            $constraintProperties['type'],
            $constraintProperties['message'],
            $constraintProperties['options']
        );

        return $this->constraintMetadataCache[$hash];
    }

    /**
     * @param Constraint $constraint
     * @return array
     */
    protected function processConstraint(Constraint $constraint)
    {
        return array(
            'message' => $this->translate($constraint->message),
            'options' => array(),
        );
    }

    /**
     * @param Constraint $constraint
     * @return array
     */
    protected function processTypeConstraint(Constraint $constraint)
    {
        /** @var $constraint Type */
        return array(
            'message' => $this->translate($constraint->message, array(
                    '{{ type }}'  => $constraint->type,
                )),
            'options' => array(
                'type' => $constraint->type
            ),
        );
    }

    /**
     * @param Constraint $constraint
     * @return array
     */
    protected function processEmailConstraint(Constraint $constraint)
    {
        /** @var $constraint Email */
        return array(
            'message' => $this->translate($constraint->message),
            'options' => array(
                'checkMX' => $constraint->checkMX,
                'checkHost' => $constraint->checkHost,
            ),
        );
    }

    /**
     * @param Constraint $constraint
     * @return array
     */
    protected function processUrlConstraint(Constraint $constraint)
    {
        /** @var $constraint Url */
        return array(
            'message' => $this->translate($constraint->message),
            'options' => array(
                'protocols' => $constraint->protocols,
            ),
        );
    }

    /**
     * @param Constraint $constraint
     * @return array
     */
    protected function processRegexConstraint(Constraint $constraint)
    {
        /** @var $constraint Regex */
        return array(
            'message' => $this->translate($constraint->message),
            'options' => array(
                'pattern' => $constraint->pattern,
                'htmlPattern' => $constraint->htmlPattern,
                'match' => $constraint->match,
            ),
        );
    }

    /**
     * @param Constraint $constraint
     * @return array
     */
    protected function processIpConstraint(Constraint $constraint)
    {
        /** @var $constraint Ip */
        return array(
            'message' => $this->translate($constraint->message),
            'options' => array(
                'version' => $constraint->version,
            ),
        );
    }

    /**
     * @param Constraint $constraint
     * @return array
     */
    protected function processAbstractComparisonConstraint(Constraint $constraint)
    {
        /** @var $constraint AbstractComparison */
        return array(
            'message' => $this->translate($constraint->message, array(
                    '{{ value }}' => $this->valueToString($constraint->value),
                    '{{ compared_value }}' => $this->valueToString($constraint->value),
                    '{{ compared_value_type }}' => $this->valueToType($constraint->value)
                )),
            'options' => array(
                'value' => $constraint->value,
            ),
        );
    }

    /**
     * @param Constraint $constraint
     * @return array
     */
    protected function processCardSchemeConstraint(Constraint $constraint)
    {
        /** @var $constraint CardScheme */
        return array(
            'message' => $this->translate($constraint->message),
            'options' => array(
                'schemes' => $constraint->schemes,
            ),
        );
    }

    /**
     * @param Constraint $constraint
     * @return array
     */
    protected function processIssnConstraint(Constraint $constraint)
    {
        /** @var $constraint Issn */
        return array(
            'message' => $this->translate($constraint->message),
            'options' => array(
                'caseSensitive' => $constraint->caseSensitive,
                'requireHyphen' => $constraint->requireHyphen,
            ),
        );
    }

    /**
     * @param Constraint $constraint
     * @return array
     */
    protected function processLengthConstraint(Constraint $constraint)
    {
        /** @var $constraint Length */
        if ($constraint->min == $constraint->max) {
            $type = ConstraintMetadataInterface::TYPE_LENGTH_EQUAL_TO;
            $message = $this->translate($constraint->exactMessage, array(
                '{{ limit }}' => $constraint->min,
            ), (int) $constraint->min);
        } elseif (null !== $constraint->max && null !== $constraint->min) {
            $type = ConstraintMetadataInterface::TYPE_LENGTH_RANGE;
            $message = $this->translate('This value should be between {{ min }} and {{ max }} characters long.', array(
                '{{ min }}' => $constraint->min,
                '{{ max }}' => $constraint->max,
            ));
        } elseif (null !== $constraint->max) {
            $type = ConstraintMetadataInterface::TYPE_LENGTH_LESS_THAN_OR_EQUAL;
            $message = $this->translate($constraint->maxMessage, array(
                '{{ limit }}' => $constraint->max,
            ), (int) $constraint->max);
        } else {
            $type = ConstraintMetadataInterface::TYPE_LENGTH_GREATER_THAN_OR_EQUAL;
            $message = $this->translate($constraint->minMessage, array(
                '{{ limit }}' => $constraint->min,
            ), (int) $constraint->min);
        }

        return array(
            'type' => $type,
            'message' => $message,
            'options' => array(
                'min' => $constraint->min,
                'max' => $constraint->max,
            ),
        );
    }

    /**
     * @param Constraint $constraint
     * @return array
     */
    protected function processRangeConstraint(Constraint $constraint)
    {
        /** @var $constraint Range */
        if (null !== $constraint->max && null !== $constraint->min) {
            $type = ConstraintMetadataInterface::TYPE_RANGE;
            $message = $this->translate('This value should be between {{ min }} and {{ max }}.', array(
                '{{ min }}' => $constraint->min,
                '{{ max }}' => $constraint->max,
            ));
        } elseif (null !== $constraint->max) {
            $type = ConstraintMetadataInterface::TYPE_RANGE_LESS_THAN_OR_EQUAL;
            $message = $this->translate($constraint->maxMessage, array(
                '{{ limit }}' => $constraint->max,
            ));
        } else {
            $type = ConstraintMetadataInterface::TYPE_RANGE_GREATER_THAN_OR_EQUAL;
            $message = $this->translate($constraint->minMessage, array(
                '{{ limit }}' => $constraint->min,
            ));
        }

        return array(
            'type' => $type,
            'message' => $message,
            'options' => array(
                'min' => $constraint->min,
                'max' => $constraint->max,
            ),
        );
    }

    /**
     * @param Constraint $constraint
     * @return array
     */
    protected function processChoiceConstraint(Constraint $constraint)
    {
        /** @var $constraint Choice */
        if ($constraint->multiple) {
            $type = ConstraintMetadataInterface::TYPE_CHOICE_MULTIPLE;
            $message = $this->translate($constraint->multipleMessage);

//            if ($constraint->min !== null && $constraint->max !== null) {
//                $message = $this->translate('This value should be between {{ min }} and {{ max }}.', array(
//                    '{{ min }}' => $constraint->min,
//                    '{{ max }}' => $constraint->max,
//                ), (int) $constraint->min);
//            } elseif ($constraint->min !== null) {
//                $message = $this->translate($constraint->minMessage, array(
//                    '{{ limit }}' => $constraint->min
//                ), (int) $constraint->min);
//            } elseif ($constraint->max !== null) {
//                $message = $this->translate($constraint->maxMessage, array(
//                    '{{ limit }}' => $constraint->max
//                ), (int) $constraint->max);
//            }
        } else {
            $type = ConstraintMetadataInterface::TYPE_CHOICE_SINGLE;
            $message = $this->translate($constraint->message);
        }

        return array(
            'type' => $type,
            'message' => $message,
            'options' => array(
                'choices' => $constraint->choices,
                'multiple' => $constraint->multiple,
                'strict' => $constraint->strict,
                'min' => $constraint->min,
                'max' => $constraint->max,
            ),
        );
    }

    /**
     * @param Constraint $constraint
     * @return array
     */
    protected function processCountConstraint(Constraint $constraint)
    {
        /** @var $constraint Count */
        if ($constraint->min == $constraint->max) {
            $type = ConstraintMetadataInterface::TYPE_COUNT_EQUAL_TO;
            $message = $this->translate($constraint->exactMessage, array(
                '{{ limit }}' => $constraint->min,
            ), (int) $constraint->min);
        } elseif (null !== $constraint->max && null !== $constraint->min) {
            $type = ConstraintMetadataInterface::TYPE_COUNT_RANGE;
            $message = $this->translate('This collection should contain between {{ min }} and {{ max }} elements.', array(
                '{{ min }}' => $constraint->min,
                '{{ max }}' => $constraint->min,
            ), (int) $constraint->min);
        } elseif (null !== $constraint->max) {
            $type = ConstraintMetadataInterface::TYPE_COUNT_LESS_THAN_OR_EQUAL;
            $message = $this->translate($constraint->maxMessage, array(
                '{{ limit }}' => $constraint->max,
            ), (int) $constraint->max);
        } else {
            $type = ConstraintMetadataInterface::TYPE_COUNT_GREATER_THAN_OR_EQUAL;
            $message = $this->translate($constraint->minMessage, array(
                '{{ limit }}' => $constraint->min,
            ), (int) $constraint->min);
        }

        return array(
            'type' => $type,
            'message' => $message,
            'options' => array(
                'min' => $constraint->min,
                'max' => $constraint->max,
            ),
        );
    }

    /**
     * Returns a string representation of the type of the value.
     *
     * @param  mixed $value
     *
     * @return string
     */
    protected function valueToType($value)
    {
        return is_object($value) ? get_class($value) : gettype($value);
    }

    /**
     * Returns a string representation of the value.
     *
     * @param  mixed  $value
     *
     * @return string
     */
    protected function valueToString($value)
    {
        if ($value instanceof \DateTime) {
            return $value->format('Y-m-d H:i:s');
        }

        return var_export($value, true);
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