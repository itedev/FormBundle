<?php

namespace ITE\FormBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class AbstractRangeType
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
abstract class AbstractRangeType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $classNormalizer = function(Options $options, $class) {
            $rangeInterface = 'ITE\FormBundle\Form\Data\RangeInterface';

            $refClass = new \ReflectionClass($class);
            if (!$refClass->implementsInterface($rangeInterface) ) {
                throw new \RuntimeException(sprintf('Class "%s" must implement "%s" interface.', $class, $rangeInterface));
            }

            return $class;
        };

        $resolver->setDefaults([
            'class' => 'ITE\FormBundle\Form\Data\Range',
        ]);
        $resolver->setNormalizers([
            'class' => $classNormalizer,
        ]);
    }
}