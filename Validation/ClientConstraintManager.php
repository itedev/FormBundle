<?php

namespace ITE\FormBundle\Validation;

use Symfony\Component\Validator\Constraint;

/**
 * Class ClientConstraintManager
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class ClientConstraintManager implements ClientConstraintManagerInterface
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
     * @var array|ConstraintTransformerInterface[][] $transformers
     */
    protected $transformers = [];

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
     * @param string $validator
     * @param ConstraintTransformerInterface $transformer
     */
    public function addTransformer($validator, ConstraintTransformerInterface $transformer)
    {
        $this->transformers[$validator][] = $transformer;
    }

    /**
     * @param string $name
     * @return bool
     */
    public function hasTransformers($name)
    {
        return array_key_exists($name, $this->transformers);
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

    /**
     * @param string $validator
     * @param ClientConstraint $constraint
     * @return array|null
     */
    public function transform($validator, ClientConstraint $constraint)
    {
        if ($this->hasTransformers($validator)) {
            throw new \RuntimeException(sprintf('No transformers for "%s" validator.', $validator));
        }
        foreach ($this->transformers[$validator] as $transformer) {
            if ($transformer->supports($constraint)) {
                return $transformer->transform($constraint);
            }
        }

        return null;
    }
}