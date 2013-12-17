<?php

namespace ITE\FormBundle\SF\Component;

use ITE\FormBundle\SF\Component;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\NodeBuilder;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Loader\FileLoader;

/**
 * Class CollectionComponent
 * @package ITE\FormBundle\SF\Component
 */
class CollectionComponent extends Component
{
    const NAME = 'collection';

    /**
     * {@inheritdoc}
     */
    public function addConfiguration(ArrayNodeDefinition $rootNode)
    {
        /** @var $node NodeBuilder */
        $node = parent::addConfiguration($rootNode);
        $node
            ->enumNode('type')
                ->values(array('bs2', 'bs3'))
            ->end()
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function loadConfiguration(FileLoader $loader, array $config, ContainerBuilder $container)
    {
        $container->setParameter('ite_form.collection.type', $config['type']);
        parent::loadConfiguration($loader, $config, $container);
    }

    /**
     * {@inheritdoc}
     */
    public function addFormResources(ContainerInterface $container)
    {
        switch ($container->getParameter('ite_form.collection.type')) {
            case 'bs2':
                $template = 'ITEFormBundle:Form/Component/collection:bs2.html.twig';
                break;
            case 'bs3':
                $template = 'ITEFormBundle:Form/Component/collection:bs3.html.twig';
                break;
        }

        return array($template);
    }

}