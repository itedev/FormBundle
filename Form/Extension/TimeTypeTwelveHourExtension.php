<?php

namespace ITE\FormBundle\Form\Extension;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\DataTransformer\DateTimeToStringTransformer;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class TimeTypeTwelveHourExtension
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class TimeTypeTwelveHourExtension extends AbstractTypeExtension
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if (!$options['twelve_hour']) {
            return;
        }

        $format = 'h';
        if ($options['with_minutes']) {
            $format .= ':i';
        }
        if ($options['with_seconds']) {
            $format .= ':s';
        }
        $format .= 'a';

        if ('single_text' === $options['widget']) {
            $viewTransformers = $builder->getViewTransformers();
            $builder->resetViewTransformers();
            foreach ($viewTransformers as $viewTransformer) {
                if ($viewTransformer instanceof DateTimeToStringTransformer) {
                    $viewTransformer = new DateTimeToStringTransformer(
                        $options['model_timezone'],
                        $options['view_timezone'],
                        $format
                    );
                }
                $builder->addViewTransformer($viewTransformer);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults([
            'twelve_hour' => false,
        ]);
        $resolver->setAllowedTypes([
            'twelve_hour' => ['bool'],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getExtendedType()
    {
        return 'time';
    }
}
