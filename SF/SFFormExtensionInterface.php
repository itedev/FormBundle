<?php


namespace ITE\FormBundle\SF;

use ITE\JsBundle\SF\SFExtensionInterface;

/**
 * Interface SFFormExtensionInterface
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
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

    /**
     * Get formBag
     *
     * @return FormBag
     */
    public function getFormBag();

} 