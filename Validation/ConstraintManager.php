<?php

namespace ITE\FormBundle\Validation;

use Symfony\Component\Validator\Constraint;
use ITE\FormBundle\Validation\Constraint as ClientConstraint;

/**
 * Class ConstraintManager
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class ConstraintManager
{
    /**
     * @var array|ConstraintConverterInterface[] $converters
     */
    protected $converters = [];

    /**
     * @var array|ConstraintProcessorInterface[] $processors
     */
    protected $processors = [];

    /**
     * @param ConstraintConverterInterface $converter
     */
    public function addConverter(ConstraintConverterInterface $converter)
    {
        $this->converters[] = $converter;
    }

    /**
     * @param ConstraintProcessorInterface $processor
     */
    public function addProcessor(ConstraintProcessorInterface $processor)
    {
        $this->processors[] = $processor;
    }

    /**
     * @param Constraint $constraint
     * @return ClientConstraint|null
     */
    public function convert(Constraint $constraint)
    {
        foreach ($this->converters as $converter) {
            if ($converter->supports($constraint)) {
                return $converter->convert($constraint);
            }
        }

        return null;
    }

    /**
     * @param ClientConstraint $constraint
     */
    public function process(ClientConstraint $constraint)
    {
        foreach ($this->processors as $processor) {
            if ($processor->supports($constraint)) {
                $processor->process($constraint);
            }
        }
    }
}