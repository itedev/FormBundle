<?php

namespace ITE\FormBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class FormPass
 * @package ITE\FormBundle\DependencyInjection\Compiler
 */
class FormPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('ite_form.form.type_guesser')) {
            return;
        }

        $definition = $container->getDefinition('ite_form.form.type_guesser');
        $guesserServiceIds = array_keys($container->findTaggedServiceIds('form.type_guesser'));
        $guesserServiceIds = array_filter($guesserServiceIds, function($guesserServiceId) {
            return $guesserServiceId !== 'ite_form.form.type_guesser';
        });

        $guessers = array_map(function($guesserServiceId) {
            return new Reference($guesserServiceId);
        }, $guesserServiceIds);

        $definition->replaceArgument(2, $guessers);
    }
}