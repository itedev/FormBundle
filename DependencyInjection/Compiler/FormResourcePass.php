<?php

namespace ITE\FormBundle\DependencyInjection\Compiler;

use ITE\FormBundle\SF\ExtensionInterface;
use ITE\FormBundle\SF\SFFormExtensionInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Class FormResourcePass
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class FormResourcePass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $resources = $container->getParameter('twig.form.resources');
        $sfForm = $container->get('ite_form.sf.extension.form');

        $sfResources = [];
        $sfResources[] = 'ITEFormBundle:Form:fields.html.twig';
        $sfResources = $this->processComponents($sfForm, $sfResources, $container);
        $sfResources = $this->processPlugins($sfForm, $sfResources, $container);

        $index = array_search('@sf_form_resources', $resources);
        if (false !== $index) {
            array_splice(
                $resources,
                $index,
                1,
                $sfResources
            );
        } else {
            $resources = array_merge($resources, $sfResources);
        }

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
            $resources = array_merge($resources, $component->addFormResources($container));
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
            $resources = array_merge($resources, $plugin->addFormResources($container));
        }

        return $resources;
    }
}
