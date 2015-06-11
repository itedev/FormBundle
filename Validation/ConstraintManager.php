<?php

namespace ITE\FormBundle\Validation;

use Symfony\Component\Validator\Constraint;

/**
 * Class ConstraintManager
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class ConstraintManager implements ConstraintManagerInterface
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
     * @param string $name
     * @param ConstraintTransformerInterface $transformer
     */
    public function addTransformer($name, ConstraintTransformerInterface $transformer)
    {
        $this->transformers[$name][] = $transformer;
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
     * @param string $name
     * @param ClientConstraint $constraint
     * @return array|null
     */
    public function transform($name, ClientConstraint $constraint)
    {
        if ($this->hasTransformers($name)) {
            throw new \RuntimeException(sprintf('No transformers for "%s" name.', $name));
        }
        foreach ($this->transformers[$name] as $transformer) {
            if ($transformer->supports($constraint)) {
                return $transformer->transform($constraint);
            }
        }

        return null;
    }
}