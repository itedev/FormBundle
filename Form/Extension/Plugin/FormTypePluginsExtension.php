<?php

namespace ITE\FormBundle\Form\Extension\Plugin;

use ITE\FormBundle\SF\Form\ClientFormTypeExtensionInterface;
use ITE\FormBundle\SF\Form\ClientFormView;
use ITE\FormBundle\SF\Plugin\InputmaskPlugin;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class FormTypePluginsExtension
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class FormTypePluginsExtension extends AbstractTypeExtension implements ClientFormTypeExtensionInterface
{
    /**
     * {@inheritdoc}
     */
    public function buildClientView(ClientFormView $clientView, FormView $view, FormInterface $form, array $options)
    {
        if (!isset($options['plugins'])) {
            return;
        }

        foreach ($options['plugins'] as $plugin => $pluginOptions) {
            $clientView->addPlugin($plugin, [
                'extras' => (object) [],
                'options' => (object) $pluginOptions,
            ]);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $pluginsNormalizer = function (Options $options, $plugins) {
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
