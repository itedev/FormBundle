<?php

namespace ITE\FormBundle\OptionsResolver;

use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class MultidimensionalOptionsResolver
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class MultidimensionalOptionsResolver extends OptionsResolver
{
    /**
     * @var array|OptionsResolver[]|MultidimensionalOptionsResolver[]
     */
    private $resolvers = [];

    /**
     * @param array|OptionsResolver[]|MultidimensionalOptionsResolver[] $resolvers
     * @return $this
     */
    public function setResolvers(array $resolvers)
    {
        foreach ($resolvers as $name => $resolver) {
            $this->setResolver($name, $resolver);
        }

        return $this;
    }

    /**
     * @param string $name
     * @param OptionsResolver $resolver
     * @return $this
     */
    public function setResolver($name, OptionsResolver $resolver)
    {
        $this->resolvers[$name] = $resolver;

        return $this;
    }

    /**
     * @param array $options
     * @return array
     */
    public function resolve(array $options = [])
    {
        $options = parent::resolve($options);
        foreach ($this->resolvers as $name => $resolver) {
            $subOptions = array_key_exists($name, $options)
                ? $options[$name]
                : [];
            $options[$name] = $resolver->resolve($subOptions);
        }

        return $options;
    }
}
