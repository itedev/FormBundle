<?php

namespace ITE\FormBundle\Service\Validation\Plugin\Parsley;

use ITE\FormBundle\Service\Validation\ConstraintExtractorInterface;
use ITE\FormBundle\Service\Validation\ConstraintMapperInterface;
use ITE\FormBundle\Service\Validation\ConstraintMetadataInterface;
use ITE\FormBundle\Service\Validation\FormConstraint;
use ITE\FormBundle\Util\FormUtils;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

/**
 * Class ConstraintMapper
 * @package ITE\FormBundle\Service\Validation\Plugin\Parsley
 */
class ConstraintMapper implements ConstraintMapperInterface
{
    /**
     * @var ConstraintExtractorInterface $constraintExtractor
     */
    protected $constraintExtractor;

    /**
     * @param ConstraintExtractorInterface $constraintExtractor
     */
    public function __construct(ConstraintExtractorInterface $constraintExtractor)
    {
        $this->constraintExtractor = $constraintExtractor;
    }

    /**
     * @param FormView $rootView
     * @param FormInterface $rootForm
     * @return array
     */
    public function map(FormView $rootView, FormInterface $rootForm)
    {
        $constraints = $this->constraintExtractor->getConstraints($rootForm);
        $result = array();
        foreach ($constraints as $constraint) {
            /** @var $constraint FormConstraint */
            /** @var $constraintMetadata ConstraintMetadataInterface */
            $constraintMetadata = $constraint->getConstraintMetadata();
            $form = $constraint->getForm();
            $view = $constraint->initializeView($rootView);
            $selector = FormUtils::generateSelector($view);

            switch ($constraintMetadata->getType()) {
                case ConstraintMetadataInterface::TYPE_NOT_BLANK:
                    $result[] = array(
                        'selector' => $selector,
                        'attr' => array(
                            'parsley-notblank' => 'true',
                            'parsley-notblank-message' => $constraintMetadata->getMessage(),
                        ),
                    );
                    break;
                case ConstraintMetadataInterface::TYPE_LENGTH_GREATER_THAN_OR_EQUAL:
                    $result[] = array(
                        'selector' => $selector,
                        'attr' => array(
                            'parsley-minlength' => $constraintMetadata->getOption('min'),
                            'parsley-minlength-message' => $constraintMetadata->getMessage(),
                        ),
                    );
                    break;
                case ConstraintMetadataInterface::TYPE_LENGTH_LESS_THAN_OR_EQUAL:
                    $result[] = array(
                        'selector' => $selector,
                        'attr' => array(
                            'parsley-maxlength' => $constraintMetadata->getOption('max'),
                            'parsley-maxlength-message' => $constraintMetadata->getMessage(),
                        ),
                    );
                    break;
                case ConstraintMetadataInterface::TYPE_LENGTH_RANGE:
                    $result[] = array(
                        'selector' => $selector,
                        'attr' => array(
                            'parsley-rangelength' => sprintf('[%s,%s]',
                                $constraintMetadata->getOption('min'), $constraintMetadata->getOption('max')),
                            'parsley-rangelength-message' => $constraintMetadata->getMessage(),
                        ),
                    );
                    break;
                case ConstraintMetadataInterface::TYPE_RANGE_GREATER_THAN_OR_EQUAL:
                case ConstraintMetadataInterface::TYPE_GREATER_THAN_OR_EQUAL:
                    $result[] = array(
                        'selector' => $selector,
                        'attr' => array(
                            'parsley-min' => $constraintMetadata->getOption('min'),
                            'parsley-min-message' => $constraintMetadata->getMessage(),
                        ),
                    );
                    break;
                case ConstraintMetadataInterface::TYPE_RANGE_LESS_THAN_OR_EQUAL:
                case ConstraintMetadataInterface::TYPE_LESS_THAN_OR_EQUAL:
                    $result[] = array(
                        'selector' => $selector,
                        'attr' => array(
                            'parsley-max' => $constraintMetadata->getOption('max'),
                            'parsley-max-message' => $constraintMetadata->getMessage(),
                        ),
                    );
                    break;
                case ConstraintMetadataInterface::TYPE_RANGE:
                    $result[] = array(
                        'selector' => $selector,
                        'attr' => array(
                            'parsley-range' => sprintf('[%s,%s]',
                                $constraintMetadata->getOption('min'), $constraintMetadata->getOption('max')),
                            'parsley-range-message' => $constraintMetadata->getMessage(),
                        ),
                    );
                    break;
                case ConstraintMetadataInterface::TYPE_COUNT_GREATER_THAN_OR_EQUAL:
                    $result[] = array(
                        'selector' => $selector,
                        'attr' => array(
                            'parsley-mincheck' => $constraintMetadata->getOption('min'),
                            'parsley-mincheck-message' => $constraintMetadata->getMessage(),
                        ),
                    );
                    break;
                case ConstraintMetadataInterface::TYPE_COUNT_LESS_THAN_OR_EQUAL:
                    $result[] = array(
                        'selector' => $selector,
                        'attr' => array(
                            'parsley-maxcheck' => $constraintMetadata->getOption('max'),
                            'parsley-maxcheck-message' => $constraintMetadata->getMessage(),
                        ),
                    );
                    break;
                case ConstraintMetadataInterface::TYPE_COUNT_RANGE:
                    $result[] = array(
                        'selector' => $selector,
                        'attr' => array(
                            'parsley-rangecheck' => sprintf('[%s,%s]',
                                $constraintMetadata->getOption('min'), $constraintMetadata->getOption('max')),
                            'parsley-rangecheck-message' => $constraintMetadata->getMessage(),
                        ),
                    );
                    break;
                case ConstraintMetadataInterface::TYPE_EMAIL:
                    $result[] = array(
                        'selector' => $selector,
                        'attr' => array(
                            'parsley-type' => 'email',
                            'parsley-type-email-message' => $constraintMetadata->getMessage(),
                        ),
                    );
                    break;
                case ConstraintMetadataInterface::TYPE_URL:
                    $result[] = array(
                        'selector' => $selector,
                        'attr' => array(
                            'parsley-type' => 'urlstrict',
                            'parsley-type-urlstrict-message' => $constraintMetadata->getMessage(),
                        ),
                    );
                    break;
                case ConstraintMetadataInterface::TYPE_TYPE:
                    switch ($constraintMetadata->getOption('type')) {
                        case 'int':
                        case 'integer':
                        case 'long':
                            // can't check for integers, because default 'digits' validator cannot handle negative
                            // numbers, so just check for any valid numbers
                        case 'float':
                        case 'double':
                        case 'real':
                        case 'numeric':
                            $result[] = array(
                                'selector' => $selector,
                                'attr' => array(
                                    'parsley-type' => 'number',
                                    'parsley-type-number-message' => $constraintMetadata->getMessage(),
                                ),
                            );
                            break;
                    }
                    break;
                case ConstraintMetadataInterface::TYPE_DATE:
                    $result[] = array(
                        'selector' => $selector,
                        'attr' => array(
                            'parsley-type' => 'dateIso',
                            'parsley-type-dateIso-message' => $constraintMetadata->getMessage(),
                        ),
                    );
                    break;
                case ConstraintMetadataInterface::TYPE_CHOICE_SINGLE:
                    $result[] = array(
                        'selector' => $selector,
                        'attr' => array(
                            'parsley-inlist' => implode(',', $constraintMetadata->getOption('choices')),
                            'parsley-inlist-message' => $constraintMetadata->getMessage(),
                        ),
                    );
                    break;
                case ConstraintMetadataInterface::TYPE_LUHN:
                    $result[] = array(
                        'selector' => $selector,
                        'attr' => array(
                            'parsley-luhn' => 'true',
                            'parsley-luhn-message' => $constraintMetadata->getMessage(),
                        ),
                    );
                    break;
                case ConstraintMetadataInterface::TYPE_REPEATED:
                    $firstName = $form->getConfig()->getOption('first_name');
                    $firstView = $view->children[$firstName];
                    $firstSelector = FormUtils::generateSelector($firstView);

                    $secondName = $form->getConfig()->getOption('second_name');
                    $secondView = $view->children[$secondName];
                    $secondSelector = FormUtils::generateSelector($secondView);

                    $result[] = array(
                        'selector' => $secondSelector,
                        'attr' => array(
                            'parsley-equalto' => $firstSelector,
                            'parsley-equalto-message' => $constraintMetadata->getMessage(),
                        ),
                    );
                    break;
                default:
            }
        }

        return $result;
    }
} 