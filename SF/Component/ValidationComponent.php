<?php

namespace ITE\FormBundle\SF\Component;

use ITE\FormBundle\SF\Component;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\NodeBuilder;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\FileLoader;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class ValidationComponent
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class ValidationComponent extends Component
{
    /**
     * {@inheritdoc}
     */
    public function addConfiguration(ArrayNodeDefinition $rootNode, ContainerBuilder $container)
    {
        /** @var $node NodeBuilder */
        $node = parent::addConfiguration($rootNode, $container);

        return $node
            ->booleanNode('enable_annotations')->defaultTrue()->end()
            ->arrayNode('static_method')
                ->defaultValue(['loadValidatorMetadata'])
                ->prototype('scalar')->end()
                ->treatFalseLike([])
                ->validate()
                    ->ifTrue(function ($v) { return !is_array($v); })
                    ->then(function ($v) { return (array) $v; })
                ->end()
            ->end()
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function loadConfiguration(FileLoader $loader, array $config, ContainerBuilder $container)
    {
        parent::loadConfiguration($loader, $config, $container);

        $factory = $container->getDefinition('ite_form.validation.mapping.class_metadata_factory.factory');

        $xmlMappings = $this->getValidatorXmlMappingFiles($container);
        $yamlMappings = $this->getValidatorYamlMappingFiles($container);

        if (count($xmlMappings) > 0) {
            $factory->addMethodCall('addXmlMappings', [$xmlMappings]);
        }

        if (count($yamlMappings) > 0) {
            $factory->addMethodCall('addYamlMappings', [$yamlMappings]);
        }

        if (array_key_exists('enable_annotations', $config) && $config['enable_annotations']) {
            $factory->addMethodCall('enableAnnotationMapping', [new Reference('annotation_reader')]);
        }

        if (array_key_exists('static_method', $config) && $config['static_method']) {
            foreach ($config['static_method'] as $methodName) {
                $factory->addMethodCall('addMethodMapping', [$methodName]);
            }
        }

        $loader->load('validator/jquery_validate.yml');

//        if (isset($config['cache'])) {
//            $container->setParameter(
//                'validator.mapping.cache.prefix',
//                'validator_'.hash('sha256', $container->getParameter('kernel.root_dir'))
//            );
//
//            $factory->addMethodCall('setMetadataCache', [new Reference($config['cache'])]);
//        }
    }

    /**
     * {@inheritdoc}
     */
    public static function getName()
    {
        return 'validation';
    }

    /**
     * @param ContainerBuilder $container
     * @return array
     */
    private function getValidatorXmlMappingFiles(ContainerBuilder $container)
    {
        $files = [];

        foreach ($container->getParameter('kernel.bundles') as $bundle) {
            $reflection = new \ReflectionClass($bundle);
            if (is_file($file = dirname($reflection->getFilename()).'/Resources/config/client_validation.xml')) {
                $files[] = realpath($file);
                $container->addResource(new FileResource($file));
            }
        }

        return $files;
    }

    /**
     * @param ContainerBuilder $container
     * @return array
     */
    private function getValidatorYamlMappingFiles(ContainerBuilder $container)
    {
        $files = [];

        foreach ($container->getParameter('kernel.bundles') as $bundle) {
            $reflection = new \ReflectionClass($bundle);
            if (is_file($file = dirname($reflection->getFilename()).'/Resources/config/client_validation.yml')) {
                $files[] = realpath($file);
                $container->addResource(new FileResource($file));
            }
        }

        return $files;
    }
}