<?php

namespace ITE\FormBundle\SF;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\DefinitionDecorator;

/**
 * Class ChoicePlugin
 * @package ITE\FormBundle\SF
 */
class ChoicePlugin extends Plugin
{
    /**
     * @param $serviceId
     * @param $pluginName
     * @param ContainerBuilder $container
     */
    protected function addExtendedChoiceTypes($serviceId, $pluginName, ContainerBuilder $container)
    {
        foreach ($this->getChoiceTypeNames() as $type) {
            $definition = new DefinitionDecorator($serviceId);
            $definition
              ->addMethodCall('setType', array($type))
              ->addTag('form.type', array(
                      'alias' => sprintf('ite_%s_%s', $pluginName, $type))
              );

            $extendedServiceId = preg_replace('/(abstract)$/', $type, $serviceId);
            $container->setDefinition($extendedServiceId, $definition);
        }
    }

    /**
     * @return array
     */
    protected function getChoiceTypeNames()
    {
        return array(
            'choice',
            'language',
            'country',
            'timezone',
            'locale',
            'currency',
            'entity',
            'document',
            'model',
        );
    }
} 