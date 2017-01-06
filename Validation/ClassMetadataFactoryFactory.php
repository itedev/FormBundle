<?php

namespace ITE\FormBundle\Validation;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\CachedReader;
use Doctrine\Common\Annotations\Reader;
use Doctrine\Common\Cache\ArrayCache;
use ITE\FormBundle\Validation\Mapping\Factory\ClassMetadataFactory;
use ITE\FormBundle\Validation\Mapping\Loader\AnnotationLoader;
use ITE\FormBundle\Validation\Mapping\Loader\LoaderChain;
use ITE\FormBundle\Validation\Mapping\Loader\StaticMethodLoader;
use ITE\FormBundle\Validation\Mapping\Loader\XmlFileLoader;
use ITE\FormBundle\Validation\Mapping\Loader\XmlFilesLoader;
use ITE\FormBundle\Validation\Mapping\Loader\YamlFileLoader;
use ITE\FormBundle\Validation\Mapping\Loader\YamlFilesLoader;
use Symfony\Component\Validator\Mapping\Cache\CacheInterface;

/**
 * Class ClassMetadataFactoryFactory
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class ClassMetadataFactoryFactory
{
    /**
     * @var array
     */
    private $xmlMappings = [];

    /**
     * @var array
     */
    private $yamlMappings = [];

    /**
     * @var array
     */
    private $methodMappings = [];

    /**
     * @var Reader|null
     */
    private $annotationReader;

    /**
     * @var CacheInterface|null
     */
    private $metadataCache;

    /**
     * @return ClassMetadataFactory
     */
    public function getClassMetadataFactory()
    {
        $loaders = [];

        if (count($this->xmlMappings) > 1) {
            $loaders[] = new XmlFilesLoader($this->xmlMappings);
        } elseif (1 === count($this->xmlMappings)) {
            $loaders[] = new XmlFileLoader($this->xmlMappings[0]);
        }

        if (count($this->yamlMappings) > 1) {
            $loaders[] = new YamlFilesLoader($this->yamlMappings);
        } elseif (1 === count($this->yamlMappings)) {
            $loaders[] = new YamlFileLoader($this->yamlMappings[0]);
        }

        foreach ($this->methodMappings as $methodName) {
            $loaders[] = new StaticMethodLoader($methodName);
        }

        if ($this->annotationReader) {
            $loaders[] = new AnnotationLoader($this->annotationReader);
        }

        $loader = null;
        if (count($loaders) > 1) {
            $loader = new LoaderChain($loaders);
        } elseif (1 === count($loaders)) {
            $loader = $loaders[0];
        }

        return new ClassMetadataFactory($loader);
    }

    /**
     * {@inheritdoc}
     */
    public function addXmlMapping($path)
    {
        $this->xmlMappings[] = $path;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function addXmlMappings(array $paths)
    {
        $this->xmlMappings = array_merge($this->xmlMappings, $paths);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function addYamlMapping($path)
    {
        $this->yamlMappings[] = $path;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function addYamlMappings(array $paths)
    {
        $this->yamlMappings = array_merge($this->yamlMappings, $paths);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function addMethodMapping($methodName)
    {
        $this->methodMappings[] = $methodName;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function addMethodMappings(array $methodNames)
    {
        $this->methodMappings = array_merge($this->methodMappings, $methodNames);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function enableAnnotationMapping(Reader $annotationReader = null)
    {
        if (null === $annotationReader) {
            if (!class_exists('Doctrine\Common\Annotations\AnnotationReader') || !class_exists('Doctrine\Common\Cache\ArrayCache')) {
                throw new \RuntimeException('Enabling annotation based constraint mapping requires the packages doctrine/annotations and doctrine/cache to be installed.');
            }

            $annotationReader = new CachedReader(new AnnotationReader(), new ArrayCache());
        }

        $this->annotationReader = $annotationReader;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function disableAnnotationMapping()
    {
        $this->annotationReader = null;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setMetadataCache(CacheInterface $cache)
    {
        $this->metadataCache = $cache;

        return $this;
    }
}
