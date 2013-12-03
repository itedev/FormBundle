<?php

namespace ITE\FormBundle;

use ITE\FormBundle\DependencyInjection\Compiler\FormResourcePass;
use ITE\FormBundle\DependencyInjection\Compiler\RouterResourcePass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Class ITEFormBundle
 * @package ITE\FormBundle
 */
class ITEFormBundle extends Bundle
{
    /**
     * @param ContainerBuilder $container
     */
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new FormResourcePass());
        $container->addCompilerPass(new RouterResourcePass());

        parent::build($container);
    }
}
