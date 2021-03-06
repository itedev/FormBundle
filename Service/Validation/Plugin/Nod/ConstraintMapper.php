<?php

namespace ITE\FormBundle\Service\Validation\Plugin\Nod;

use ITE\FormBundle\Service\Validation\ConstraintExtractorInterface;
use ITE\FormBundle\Service\Validation\ConstraintMapperInterface;
use ITE\FormBundle\Service\Validation\ConstraintMetadataInterface;
use ITE\FormBundle\Service\Validation\FormConstraint;
use ITE\FormBundle\Util\FormUtils;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

/**
 * Class ConstraintMapper
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
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
                case ConstraintMetadataInterface::TYPE_NOT_NULL:
                    $result[] = array(
                        $selector,
                        'presence',
                        $constraintMetadata->getMessage()
                    );
                    break;
                case ConstraintMetadataInterface::TYPE_EMAIL:
                    $result[] = array(
                        $selector,
                        'email',
                        $constraintMetadata->getMessage()
                    );
                    break;
                case ConstraintMetadataInterface::TYPE_EQUAL_TO:
                case ConstraintMetadataInterface::TYPE_IDENTICAL_TO:
                    $result[] = array(
                        $selector,
                        'exact:' . $constraintMetadata->getOption('value'),
                        $constraintMetadata->getMessage()
                    );
                    break;
                case ConstraintMetadataInterface::TYPE_NOT_EQUAL_TO:
                case ConstraintMetadataInterface::TYPE_NOT_IDENTICAL_TO:
                    $result[] = array(
                        $selector,
                        'not:' . $constraintMetadata->getOption('value'),
                        $constraintMetadata->getMessage()
                    );
                    break;
                case ConstraintMetadataInterface::TYPE_LENGTH_LESS_THAN_OR_EQUAL:
                    $result[] = array(
                        $selector,
                        'max-length:' . $constraintMetadata->getOption('max'),
                        $constraintMetadata->getMessage()
                    );
                    break;
                case ConstraintMetadataInterface::TYPE_LENGTH_GREATER_THAN_OR_EQUAL:
                    $result[] = array(
                        $selector,
                        'min-length:' . $constraintMetadata->getOption('min'),
                        $constraintMetadata->getMessage()
                    );
                    break;
                case ConstraintMetadataInterface::TYPE_LENGTH_EQUAL_TO:
                    $result[] = array(
                        $selector,
                        'exact-length:' . $constraintMetadata->getOption('min'),
                        $constraintMetadata->getMessage()
                    );
                    break;
                case ConstraintMetadataInterface::TYPE_LENGTH_RANGE:
                    $result[] = array(
                        $selector,
                        'between:' . $constraintMetadata->getOption('min') . ':' . $constraintMetadata->getOption('max'),
                        $constraintMetadata->getMessage()
                    );
                    break;
                case ConstraintMetadataInterface::TYPE_RANGE_LESS_THAN_OR_EQUAL:
                case ConstraintMetadataInterface::TYPE_LESS_THAN_OR_EQUAL:
                    $result[] = array(
                        $selector,
                        'max-num:' . $constraintMetadata->getOption('max'),
                        $constraintMetadata->getMessage()
                    );
                    break;
                case ConstraintMetadataInterface::TYPE_RANGE_GREATER_THAN_OR_EQUAL:
                case ConstraintMetadataInterface::TYPE_GREATER_THAN_OR_EQUAL:
                    $result[] = array(
                        $selector,
                        'min-num:' . $constraintMetadata->getOption('min'),
                        $constraintMetadata->getMessage()
                    );
                    break;
                case ConstraintMetadataInterface::TYPE_RANGE:
                    $result[] = array(
                        $selector,
                        'between-num:' . $constraintMetadata->getOption('min') . ':' . $constraintMetadata->getOption('max'),
                        $constraintMetadata->getMessage()
                    );
                    break;
                case ConstraintMetadataInterface::TYPE_TYPE:
                    switch ($constraintMetadata->getOption('type')) {
                        case 'int':
                        case 'integer':
                        case 'long':
                            $result[] = array(
                                $selector,
                                'integer',
                                $constraintMetadata->getMessage()
                            );
                            break;
                        case 'float':
                        case 'double':
                        case 'real':
                        case 'numeric':
                            $result[] = array(
                                $selector,
                                'float',
                                $constraintMetadata->getMessage()
                            );
                            break;
                    }

                    break;
                case ConstraintMetadataInterface::TYPE_REPEATED:
                    $firstName = $form->getConfig()->getOption('first_name');
                    $firstView = $view->children[$firstName];
                    $firstSelector = FormUtils::generateSelector($firstView);

                    $secondName = $form->getConfig()->getOption('second_name');
                    $secondView = $view->children[$secondName];
                    $secondSelector = FormUtils::generateSelector($secondView);

//                    $result[] = array(
//                        $firstSelector,
//                        'same-as:' . $secondSelector,
//                        $constraintMetadata->getMessage()
//                    );

                    $result[] = array(
                        $secondSelector,
                        'same-as:' . $firstSelector,
                        $constraintMetadata->getMessage()
                    );
                    break;
                default:

            }
        }

        return $result;
    }
} 