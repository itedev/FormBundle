<?php

namespace ITE\FormBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Yaml\Yaml;

/**
 * Class RouterResourcePass
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class RouterResourcePass implements CompilerPassInterface
{
    /**
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        $routerResource = $container->getParameterBag()->resolveValue($container->getParameter('router.resource'));
        if (!$routerResource) {
            return;
        }

        $file = $container->getParameter('kernel.cache_dir') . '/ite_form/routing.yml';

        if (!is_dir($dir = dirname($file))) {
            mkdir($dir, 0777, true);
        }

        file_put_contents($file, Yaml::dump(array(
            '_ite_form' => array('resource' => '.', 'type' => 'ite_form'),
            '_app'     => array('resource' => $container->getParameter('router.resource')),
        )));

        $container->setParameter('router.resource', $file);
    }
}
