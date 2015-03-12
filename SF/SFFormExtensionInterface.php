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
     * @param ExtensionInterface $component
     */
    public function addComponent(ExtensionInterface $component);

    /**
     * Get components
     *
     * @return array
     */
    public function getComponents();

    /**
     * @param ExtensionInterface $plugin
     */
    public function addPlugin(ExtensionInterface $plugin);

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