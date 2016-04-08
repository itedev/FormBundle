<?php

namespace ITE\FormBundle\CacheWarmer;

use ITE\FormBundle\Proxy\ProxyFactory;
use Symfony\Component\HttpKernel\CacheWarmer\CacheWarmerInterface;

/**
 * Class ProxyCacheWarmer
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class ProxyCacheWarmer implements CacheWarmerInterface
{
    /**
     * @var ProxyFactory $proxyFactory
     */
    private $proxyFactory;

    /**
     * @param ProxyFactory $proxyFactory
     */
    public function __construct(ProxyFactory $proxyFactory)
    {
        $this->proxyFactory = $proxyFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function warmUp($cacheDir)
    {
        $classes = [
            'ITE\FormBundle\Form\Form',
        ];
        foreach ($classes as $class) {
            $reflection = new \ReflectionClass($class);
            $instance = $reflection->newInstanceWithoutConstructor();
            $this->proxyFactory->createProxy($instance);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function isOptional()
    {
        return false;
    }
}
