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
    protected $proxyFactory;

    /**
     * @var array $classes
     */
    protected $classes;

    /**
     * @param ProxyFactory $proxyFactory
     * @param array $classes
     */
    public function __construct(ProxyFactory $proxyFactory, array $classes)
    {
        $this->proxyFactory = $proxyFactory;
        $this->classes = $classes;
    }

    /**
     * {@inheritdoc}
     */
    public function warmUp($cacheDir)
    {
        $classes = [$this->classes['form']];
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
