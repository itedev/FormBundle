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
        $pluginsNormalizer = function (Options $options, $plugins) {
            if (!isset($plugins)) {
                return array();
            }

            $normalizedPlugins = array();
            if (is_array($plugins)) {
                foreach ($plugins as $plugin => $pluginOptions) {
                    if (is_int($plugin)) {
                        if (is_string($pluginOptions)) {
                            $normalizedPlugins[$pluginOptions] = array();
                        }
                    } else {
                        $normalizedPlugins[$plugin] = is_array($pluginOptions) ? $pluginOptions : array();
                    }
                }
            } else {
                $normalizedPlugins[$plugins] = array();
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
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        if (!$form->isRoot()) {
            return;
        }
        if (!isset($view->vars['attr']['id'])) {
            $view->vars['attr']['id'] = $view->vars['id'] . '_form';
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getExtendedType()
    {
        return 'form';
    }
} 