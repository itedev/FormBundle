<?php

namespace ITE\FormBundle\SF\Component;

use ITE\FormBundle\SF\AbstractComponent;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\FileLoader;

/**
 * Class AjaxSubmitComponent
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class AjaxSubmitComponent extends AbstractComponent
{
    /**
     * {@inheritdoc}
     */
    public function addConfiguration(ContainerBuilder $container)
    {
        $rootNode = parent::addConfiguration($container);

        $rootNode
            ->children()
                ->scalarNode('default_submitter')
                    ->cannotBeEmpty()
                    ->isRequired()
                ->end()
            ->end()
        ;

        return $rootNode;
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
