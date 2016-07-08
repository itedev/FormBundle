<?php

namespace ITE\FormBundle\Proxy;

use ProxyManager\Autoloader\AutoloaderInterface;
use ProxyManager\Configuration;
use ProxyManager\Factory\AccessInterceptorValueHolderFactory;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Class ProxyFactory
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class ProxyFactory extends AccessInterceptorValueHolderFactory
{
    /**
     * @var Filesystem $fs
     */
    private $fs;

    /**
     * @var string $proxyDir
     */
    private $proxyDir;

    /**
     * @param string $proxyDir
     */
    public function __construct(Filesystem $fs, $proxyDir)
    {
        $this->fs = $fs;
        $this->proxyDir = $proxyDir;

        if (!$this->fs->exists($this->proxyDir)) {
            $this->fs->mkdir($this->proxyDir);
        }

        $configuration = new Configuration();
        $configuration->setProxiesTargetDir($proxyDir);
        $configuration->setProxiesNamespace('Proxy');
        parent::__construct($configuration);
    }

    /**
     * @return string
     */
    public function getProxyDir()
    {
        return $this->proxyDir;
    }

    /**
     * @return AutoloaderInterface
     */
    public function getProxyAutoloader()
    {
        return $this->configuration->getProxyAutoloader();
    }
}
