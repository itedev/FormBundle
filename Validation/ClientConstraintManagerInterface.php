<?php

namespace ITE\FormBundle\Validation;

use Symfony\Component\Validator\Constraint;

/**
 * Interface ClientConstraintManagerInterface
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
interface ClientConstraintManagerInterface
{
    /**
     * @param ConstraintConverterInterface $converter
     */
    public function addConverter(ConstraintConverterInterface $converter);

    /**
     * @param ConstraintProcessorInterface $processor
     */
    public function addProcessor(ConstraintProcessorInterface $processor);

    /**
     * @param string $name
     * @param ConstraintTransformerInterface $transformer
     */
    public function addTransformer($name, ConstraintTransformerInterface $transformer);

    /**
     * @param Constraint $constraint
     * @return ClientConstraint|null
     */
    public function convert(Constraint $constraint);

    /**
     * @param ClientConstraint $constraint
     */
    public function process(ClientConstraint $constraint);

    /**
     * @param string $name
     * @param ClientConstraint $constraint
     * @return array|null
     */
    public function transform($name, ClientConstraint $constraint);
}
