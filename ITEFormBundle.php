<?php

namespace ITE\FormBundle;

use ITE\FormBundle\DependencyInjection\Compiler\EntityConverterPass;
use ITE\FormBundle\DependencyInjection\Compiler\FormPass;
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
        $container->addCompilerPass(new FormPass());
        $container->addCompilerPass(new FormResourcePass());
        $container->addCompilerPass(new RouterResourcePass());
        $container->addCompilerPass(new EntityConverterPass());

        parent::build($container);
    }
}
