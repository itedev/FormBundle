<?php

namespace ITE\FormBundle\SF\Plugin;

use ITE\FormBundle\SF\ChoicePlugin;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\FileLoader;

/**
 * Class Select2Plugin
 * @package ITE\FormBundle\SF\Plugin
 */
class Select2Plugin extends ChoicePlugin
{
    /**
     * {@inheritdoc}
     */
    public function loadConfiguration(FileLoader $loader, array $config, ContainerBuilder $container)
    {
        parent::loadConfiguration($loader, $config, $container);

        $this->addExtendedChoiceTypes(sprintf('ite_form.form.type.plugin.%s.abstract', static::getName()), static::getName(), $container);
    }

    /**
     * {@inheritdoc}
     */
    public static function getName()
    {
        return 'select2';
    }
}