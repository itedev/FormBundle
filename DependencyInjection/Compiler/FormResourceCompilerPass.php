<?php

namespace ITE\FormBundle\DependencyInjection\Compiler;

use ITE\FormBundle\SF\ExtensionInterface;
use ITE\FormBundle\SF\SFFormExtensionInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Class FormResourceCompilerPass
 * @package ITE\FormBundle\DependencyInjection\Compiler
 */
class FormResourceCompilerPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $resources = $container->getParameter('twig.form.resources');
        $resources[] = 'ITEFormBundle:Form:fields.html.twig';

        $sfForm = $container->get('ite_form.sf.extension.form');
        $resources = $this->processComponents($sfForm, $resources, $container);
        $resources = $this->processPlugins($sfForm, $resources, $container);

        $container->setParameter('twig.form.resources', $resources);
    }

    /**
     * @param SFFormExtensionInterface $sfForm
     * @param array $resources
     * @param ContainerBuilder $container
     * @return array
     */
    protected function processComponents(SFFormExtensionInterface $sfForm, array $resources, ContainerBuilder $container)
    {
        foreach ($sfForm->getComponents() as $component) {
            /** @var $component ExtensionInterface */
            if ($component->isEnabled($container)) {
                $resources = array_merge($resources, $component->addFormResources($container));
            }
        }

        return $resources;
    }

    /**
     * @param SFFormExtensionInterface $sfForm
     * @param array $resources
     * @param ContainerBuilder $container
     * @return array
     */
    protected function processPlugins(SFFormExtensionInterface $sfForm, array $resources, ContainerBuilder $container)
    {
        foreach ($sfForm->getPlugins() as $plugin) {
            /** @var $plugin ExtensionInterface */
            if ($plugin->isEnabled($container)) {
                $resources = array_merge($resources, $plugin->addFormResources($container));
            }
        }

        return $resources;
    }
}
