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
 * Class AjaxSubmitComponent
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class AjaxSubmitComponent extends Component
{
    /**
     * {@inheritdoc}
     */
    public function addConfiguration(ArrayNodeDefinition $rootNode, ContainerBuilder $container)
    {
        $node = parent::addConfiguration($rootNode, $container);

        return $node
            ->scalarNode('default_submitter')->cannotBeEmpty()->isRequired()->end()
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function loadConfiguration(FileLoader $loader, array $config, ContainerBuilder $container)
    {
        $container->setParameter('ite_form.ajax_submit.default_submitter', $config['default_submitter']);

        parent::loadConfiguration($loader, $config, $container);
    }

    /**
     * {@inheritdoc}
     */
    public function getJavascripts()
    {
        return ['@ITEFormBundle/Resources/public/js/component/AjaxSubmit/ajax_submit.js'];
    }

    /**
     * {@inheritdoc}
     */
    public static function getName()
    {
        return 'ajax_submit';
    }
}