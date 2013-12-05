<?php

namespace ITE\FormBundle\Form\Extension\Plugin;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class FormTypeExtension
 * @package ITE\FormBundle\Form\Extension\Plugin
 */
class FormTypeExtension extends AbstractTypeExtension
{
    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $pluginsNormalizer = function (Options $options, $plugins) {
            $normalizedPlugins = array();
            foreach ($plugins as $plugin => $pluginOptions) {
                if (is_int($plugin)) {
                    if (is_string($pluginOptions)) {
                        $normalizedPlugins[$pluginOptions] = array();
                    }
                } else {
                    $normalizedPlugins[$plugin] = is_array($pluginOptions) ? $pluginOptions : array();
                }
            }

            return $normalizedPlugins;
        };

        $resolver->setOptional(array(
            'plugins'
        ));
//        $resolver->setDefaults(array(
//            'plugins' => array()
//        ));
        $resolver->setAllowedTypes(array(
            'plugins' => array('array'),
        ));
        $resolver->setNormalizers(array(
            'plugins' => $pluginsNormalizer,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getExtendedType()
    {
        return 'form';
    }
} 