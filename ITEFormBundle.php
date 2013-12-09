<?php

namespace ITE\FormBundle;

use ITE\FormBundle\DependencyInjection\Compiler\ExtensionCompilerPass;
use ITE\FormBundle\DependencyInjection\Compiler\FormResourceCompilerPass;
use ITE\FormBundle\DependencyInjection\Compiler\RouterResourceCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Class ITEFormBundle
 * @package ITE\FormBundle
 */
class ITEFormBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new ExtensionCompilerPass());
        $container->addCompilerPass(new FormResourceCompilerPass());
        $container->addCompilerPass(new RouterResourceCompilerPass());

        parent::build($container);
    }
}
