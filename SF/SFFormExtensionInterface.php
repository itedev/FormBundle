<?php


namespace ITE\FormBundle\SF;

use ITE\JsBundle\SF\SFExtensionInterface;

/**
 * Interface SFFormExtensionInterface
 * @package ITE\FormBundle\SF
 */
interface SFFormExtensionInterface extends SFExtensionInterface
{
    /**
     * @param string $alias
     * @param ExtensionInterface $component
     */
    public function addComponent($alias, ExtensionInterface $component);

    /**
     * Get components
     *
     * @return array
     */
    public function getComponents();

    /**
     * @param string $alias
     * @param ExtensionInterface $plugin
     */
    public function addPlugin($alias, ExtensionInterface $plugin);

    /**
     * Get plugins
     *
     * @return array
     */
    public function getPlugins();

    /**
     * Get elementBag
     *
     * @return ElementBag
     */
    public function getElementBag();

} 