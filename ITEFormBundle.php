<?php

namespace ITE\FormBundle;

use ITE\FormBundle\DependencyInjection\Compiler\EntityConverterPass;
use ITE\FormBundle\DependencyInjection\Compiler\FormTypeGuesserPass;
use ITE\FormBundle\DependencyInjection\Compiler\SFFromExtensionPass;
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
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new SFFromExtensionPass());
        $container->addCompilerPass(new FormResourcePass()); // must be placed AFTER SFFromExtensionPass!
        $container->addCompilerPass(new FormTypeGuesserPass());
        $container->addCompilerPass(new RouterResourcePass());
        $container->addCompilerPass(new EntityConverterPass());

        parent::build($container);
    }
}
