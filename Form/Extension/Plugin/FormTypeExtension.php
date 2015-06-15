<?php

namespace ITE\FormBundle\Form\Extension\Plugin;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class FormTypeExtension
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class FormTypeExtension extends AbstractTypeExtension
{
    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $pluginsNormalizer = function(Options $options, $plugins) {
            if (!isset($plugins)) {
                return [];
            }

            $normalizedPlugins = [];
            if (is_array($plugins)) {
                foreach ($plugins as $plugin => $pluginOptions) {
                    if (is_int($plugin)) {
                        if (is_string($pluginOptions)) {
                            $normalizedPlugins[$pluginOptions] = [];
                        }
                    } else {
                        $normalizedPlugins[$plugin] = is_array($pluginOptions) ? $pluginOptions : [];
                    }
                }
            } else {
                $normalizedPlugins[$plugins] = [];
            }

            return $normalizedPlugins;
        };

        $resolver->setOptional([
            'plugins'
        ]);
//        $resolver->setDefaults(array(
//            'plugins' => array()
//        ));
        $resolver->setAllowedTypes([
            'plugins' => ['array'],
        ]);
        $resolver->setNormalizers([
            'plugins' => $pluginsNormalizer,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getExtendedType()
    {
        return 'form';
    }
} 