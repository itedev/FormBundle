<?php

namespace ITE\FormBundle;

use ITE\FormBundle\DependencyInjection\Compiler\Component\Validation\ConstraintPass;
use ITE\FormBundle\DependencyInjection\Compiler\EntityConverterPass;
use ITE\FormBundle\DependencyInjection\Compiler\FormTypeGuesserPass;
use ITE\FormBundle\DependencyInjection\Compiler\SFFormExtensionPass;
use ITE\FormBundle\DependencyInjection\Compiler\FormResourcePass;
use ITE\FormBundle\DependencyInjection\Compiler\RouterResourcePass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Class ITEFormBundle
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class ITEFormBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        $proxyFactory = $this->container->get('ite_form.proxy_factory');
        spl_autoload_register($proxyFactory->getProxyAutoloader());

        parent::boot();
    }

    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new SFFormExtensionPass());
        $container->addCompilerPass(new FormResourcePass()); // must be placed AFTER SFFormExtensionPass!
        $container->addCompilerPass(new FormTypeGuesserPass());
        $container->addCompilerPass(new RouterResourcePass());
        $container->addCompilerPass(new EntityConverterPass());
        $container->addCompilerPass(new ConstraintPass());

        parent::build($container);
    }
}
