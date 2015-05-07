<?php

namespace ITE\FormBundle\Validation;

use Symfony\Component\Validator\Constraint as ServerConstraint;

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
     * @param ServerConstraint $constraint
     * @return Constraint|null
     */
    public function convert(ServerConstraint $constraint)
    {
        foreach ($this->converters as $converter) {
            if ($converter->supports($constraint)) {
                return $converter->convert($constraint);
            }
        }

        return null;
    }

    /**
     * @param Constraint $constraint
     */
    public function process(Constraint $constraint)
    {
        foreach ($this->processors as $processor) {
            if ($processor->supports($constraint)) {
                $processor->process($constraint);
            }
        }
    }
}