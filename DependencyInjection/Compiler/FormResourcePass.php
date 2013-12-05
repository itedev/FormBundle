<?php

namespace ITE\FormBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Class FormResourcePass
 * @package ITE\FormBundle\DependencyInjection\Compiler
 */
class FormResourcePass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $resources = $container->getParameter('twig.form.resources');
        array_splice($resources, 1, 0, array('ITEFormBundle:Form:fields.html.twig'));
//        $resources[] = 'ITEFormBundle:Form:fields.html.twig';
        if ($container->getParameter('ite_form.component.collection.enabled')) {
            $resources[] = 'ITEFormBundle:Form/Component/collection:fields.html.twig';
        }
        $container->setParameter('twig.form.resources', $resources);
    }
}
